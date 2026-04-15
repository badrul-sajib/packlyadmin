<?php

namespace Modules\Api\V1\Merchant\Message\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Message\Http\Requests\MessageRequest;
use App\Jobs\PushNotification;
use App\Models\Message\Message;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:send-help-message')->only('store');
        $this->middleware('shop.permission:show-help-messages')->only('index');
    }

    public function store(MessageRequest $request): JsonResponse
    {
        $request->validated();

        $message              = new Message;
        $message->merchant_id = auth()->user()->merchant->id;
        $message->phone       = $request->phone;
        $message->message     = $request->message;
        $message->save();

        $notificationMessage = 'New Help request from ' . auth()->user()->name . '.';

        try {
            PushNotification::dispatch([
                'title'      => 'New Help Request',
                'message'    => $notificationMessage,
                'type'       => 'info',
                'action_url' => '/help-requests',
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        return ApiResponse::success('Message sent successfully', [], Response::HTTP_OK);
    }

    public function index(Request $request): JsonResponse
    {
        $perPage  = $request->perPage ?? 10;
        $page     = $request->page    ?? 1;
        $messages = Message::where('merchant_id', auth()->user()->merchant->id)->latest()
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();

        return ApiResponse::success('Messages', $messages, Response::HTTP_OK);
    }
}
