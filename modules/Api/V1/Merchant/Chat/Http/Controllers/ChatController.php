<?php

namespace Modules\Api\V1\Merchant\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Chat\Http\Requests\ChatRequest;
use App\Models\Chat\Chat;
use App\Models\Chat\Conversation;
use App\Models\Chat\ConversationUser;
use App\Models\User\User;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class ChatController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);

        $authUser = $request->user();

        $conversations = $authUser->conversations()
            ->with(['chats' => function ($query) {
                $query->latest()->limit(1);
            }, 'users'])
            ->paginate($perPage);

        $formatedConversations = $conversations->getCollection()->map(function ($conversation) use ($authUser) {

            $user     = $conversation->users->where('id', '!=', $authUser->id)->first();
            $lastChat = $conversation->chats->last();
            $date     = $conversation->created_at;

            return [
                'id'      => $conversation->id,
                'user'    => $user->name,
                'user_id' => $user->id,
                'date'    => $date->isToday()
                           ? 'Today '.$date->format('g:i A') : ($date->isYesterday()
                           ? 'Yesterday '.$date->format('g:i A') : $date->format('Y/m/d H:i')),
                'message'      => $lastChat->message,
                'new_messages' => $conversation->chats->where('user_id', '!=', $user->id)->where('is_seen', false)->count(),
                'is_sender'    => $lastChat->user_id == $authUser->id,
            ];

        });

        $conversations = new LengthAwarePaginator($formatedConversations, $conversations->total(), $conversations->perPage(), $conversations->currentPage(), ['path' => $conversations->path()]);

        return ApiResponse::formatPagination('conversations retrieved successfully', $conversations, Response::HTTP_OK);
    }

    public function show(int $conversationId, Request $request): JsonResponse
    {
        $authUser = auth()->user();

        $conversation = Conversation::with(['chats' => function ($query) {
            $query->latest();
        }, 'users'])->findOrFail($conversationId);

        $perPage = $request->input('per_page', 10);

        $chats = $conversation->chats()->latest()->paginate($perPage);
        // Mark all messages as seen
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

        return ApiResponse::formatPagination('conversation retrieved successfully', $chats, Response::HTTP_OK);
    }

    public function sendMessage(ChatRequest $request): JsonResponse
    {
        $request->validated();

        $user = $request->user();

        $receiver = User::findOrFail($request->receiver_id);

        $conversation = Conversation::whereHas('users', function ($query) use ($user, $receiver) {
            $query->whereIn('user_id', [$user->id, $receiver->id]);
        })->first();

        if (! $conversation) {
            $conversation = Conversation::create();
            $conversation->users()->attach([$user->id, $receiver->id]);
        }

        $conversationUser = ConversationUser::where('conversation_id', $conversation->id)->where('user_id', $receiver->id)->first();

        if ($conversationUser && $conversationUser->is_blocked) {
            return ApiResponse::failure('Cannot send message. User has blocked you!', Response::HTTP_FORBIDDEN);
        }

        $chat                  = new Chat;
        $chat->conversation_id = $conversation->id;
        $chat->user_id         = $user->id;
        $chat->message         = $request->message;
        $chat->type            = 'text';
        $chat->is_seen         = false;
        $chat->save();

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
            'created_at'      => $chat->created_at,
            'unread_count'    => $conversation->chats()->where('user_id', '!=', $user->id)->where('is_seen', false)->count(),
        ]));

        return ApiResponse::success('Message sent successfully', Response::HTTP_OK);
    }

    public function blockUser(Request $request): JsonResponse
    {
        $conversationUser             = ConversationUser::where('conversation_id', $request->conversation_id)->where('user_id', $request->user_id)->first();
        $conversationUser->is_blocked = true;
        $conversationUser->save();

        return ApiResponse::success('User blocked successfully', Response::HTTP_OK);
    }

    public function unblockUser(Request $request): JsonResponse
    {
        $conversationUser             = ConversationUser::where('conversation_id', $request->conversation_id)->where('user_id', $request->user_id)->first();
        $conversationUser->is_blocked = false;
        $conversationUser->save();

        return ApiResponse::success('User unblocked successfully', Response::HTTP_OK);
    }

    public function markAsSeen(Request $request): JsonResponse
    {
        $conversation = Conversation::findOrFail($request->conversation_id);
        $conversation->chats()->where('user_id', '!=', $request->user_id)->update(['is_seen' => true]);

        return ApiResponse::success('Messages marked as seen successfully', Response::HTTP_OK);
    }

    public function users(): JsonResponse
    {
        $users = User::get();

        return ApiResponse::success('Users retrieved successfully', $users, Response::HTTP_OK);
    }
}
