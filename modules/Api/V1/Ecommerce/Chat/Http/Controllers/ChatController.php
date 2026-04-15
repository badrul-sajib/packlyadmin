<?php

namespace Modules\Api\V1\Ecommerce\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Ecommerce\Chat\Http\Requests\SendMessageRequest;
use App\Models\Chat\Chat;
use App\Models\Chat\Conversation;
use App\Models\Chat\ConversationUser;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redis;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $perPage  = $request->input('per_page', 10);
        $authUser = $request->user();

        $conversations = $authUser->conversations()
            ->with('users')
            ->paginate($perPage);

        $formatedConversations = $conversations->getCollection()->map(function ($conversation) use ($authUser) {

            $user = $conversation->users->where('id', '!=', $authUser->id)->first();

            $lastChat = $conversation->chats()->latest()->first();

            $date          = $conversation->created_at;
            $formattedDate = $date->isToday()
                ? 'Today '.$date->format('g:i A')
                : ($date->isYesterday()
                    ? 'Yesterday '.$date->format('g:i A')
                    : $date->format('Y/m/d H:i'));

            return [
                'id'           => $conversation->id,
                'user'         => $user->name,
                'user_id'      => $user->id,
                'date'         => $formattedDate,
                'message'      => $lastChat ? $lastChat->message : '', // Handle empty conversations
                'new_messages' => $conversation->chats->where('user_id', '!=', $user->id)->where('is_seen', false)->count(),
                'is_sender'    => $lastChat && $lastChat->user_id == $authUser->id,
            ];
        });

        // Recreate the pagination with the formatted data
        $conversations = new LengthAwarePaginator(
            $formatedConversations,
            $conversations->total(),
            $conversations->perPage(),
            $conversations->currentPage(),
            ['path' => $conversations->path()]
        );

        return formatPagination('Conversations retrieved successfully', $conversations);
    }

    public function show($conversationId, Request $request)
    {
        $authUser = auth()->user();

        $conversation = Conversation::findOrFail($conversationId);

        $perPage = $request->input('per_page', 10);

        $chats = $conversation->chats()->latest()->paginate($perPage);

        $conversation->chats()->where('user_id', '!=', $authUser->id)->update(['is_seen' => true]);

        $formatedChats = $chats->getCollection()->map(function ($chat) use ($authUser) {
            return [
                'id'        => $chat->id,
                'user_id'   => $chat->user_id,
                'message'   => $chat->message,
                'is_sender' => $chat->user_id == $authUser->id,
                'is_seen'   => (bool) $chat->is_seen,
            ];
        });

        $chats = new LengthAwarePaginator($formatedChats, $chats->total(), $chats->perPage(), $chats->currentPage(), ['path' => $chats->path()]);

        return formatPagination('Messages retrieved successfully', $chats);
    }

    public function sendMessage(SendMessageRequest $request)
    {
        $request->validated();

        $user = $request->user();

        $receiver = User::findOrFail($request->receiver_id);

        $conversation = Conversation::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->whereHas('users', function ($query) use ($receiver) {
                $query->where('user_id', $receiver->id);
            })
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create();
            $conversation->users()->attach([$user->id, $receiver->id]);
        }

        $conversationUser = ConversationUser::where('conversation_id', $conversation->id)->where('user_id', $receiver->id)->first();

        if ($conversationUser && $conversationUser->is_blocked) {
            return failure('Cannot send message. User has blocked you!');
        }

        $chat                  = new Chat;
        $chat->conversation_id = $conversation->id;
        $chat->user_id         = $user->id;
        $chat->message         = $request->message;
        $chat->type            = 'text';
        $chat->is_seen         = false;
        $chat->save();

        // receiver tokens
        $receiverTokens = $receiver->socketTokens->pluck('token')->toArray();

        Redis::publish('message', json_encode([
            'id'              => $chat->id,
            'conversation_id' => $conversation->id,
            'message'         => $request->message,
            'sender_name'     => $user->name,
            'type'            => 'text',
            'is_seen'         => false,
            'is_sender'       => false,
            'sender_id'       => $user->id,
            'receiver_id'     => $receiver->id,
            'receiver_tokens' => $receiverTokens,
            'created_at'      => $chat->created_at,
            'unread_count'    => $conversation->chats()->where('user_id', '!=', $user->id)->where('is_seen', false)->count(),
        ]));

        return success('Message sent successfully');
    }

    public function blockUser(Request $request)
    {
        $conversationUser             = ConversationUser::where('conversation_id', $request->conversation_id)->where('user_id', $request->user_id)->first();
        $conversationUser->is_blocked = true;
        $conversationUser->save();

        return success('User blocked successfully');
    }

    public function unblockUser(Request $request)
    {
        $conversationUser             = ConversationUser::where('conversation_id', $request->conversation_id)->where('user_id', $request->user_id)->first();
        $conversationUser->is_blocked = false;
        $conversationUser->save();

        return success('User unblocked successfully');
    }

    public function markAsSeen(Request $request)
    {
        $conversation = Conversation::findOrFail($request->conversation_id);
        $conversation->chats()->where('user_id', '!=', $request->user_id)->update(['is_seen' => true]);

        return success('Messages marked as seen successfully');
    }

    public function users(Request $request)
    {
        $users = User::where('id', '!=', $request->user()->id)->get();

        return success('Users retrieved successfully', $users);
    }
}
