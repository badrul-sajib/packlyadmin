<?php

namespace App\Http\Controllers\Admin\Reel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReelUserActionRequest;
use App\Models\Merchant\MerchantFollower;
use App\Models\Reel\Reel;
use App\Models\Reel\ReelUserAction;
use App\Services\ReelService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReelController extends Controller
{
    protected ReelService $reelService;

    public function __construct(ReelService $reelService)
    {
        $this->reelService = $reelService;
        $this->middleware('permission:reel-list')->only('index');
        $this->middleware('permission:reel-update')->only('update');
    }

    public function index(Request $request)
    {
        try {
            $reels = $this->reelService->getAllReels($request);
            if ($request->ajax()) {
                return view('components.reels.table', ['entity' => $reels])->render();
            }

            return view('Admin::reels.index', compact('reels'));
        } catch (Exception $e) {
            return redirect()->back()->with('message', 'Something went wrong. Please try again.');
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $reel = $this->reelService->updateReelStatus($id, $request->status);
            if (! $reel) {
                return failure('Reel not found', 404);
            }

            return success('Reel updated successfully', $reel);
        } catch (Exception $e) {
            return redirect()->back()->with('message', 'Something went wrong. Please try again.');
        }
    }

    public function userAction(ReelUserActionRequest $request)
    {
        $data   = $request->validated();
        $userId = auth()->id();

        switch ($data['action_type']) {
            case 'message':
                ReelUserAction::create([
                    'user_id'     => $userId,
                    'reel_id'     => $data['reel_id'],
                    'action_type' => 'message',
                    'message'     => $data['message'],
                ]);

                return response()->json([
                    'message' => 'Message sent successfully.',

                ], Response::HTTP_CREATED);

            case 'block':
                ReelUserAction::create([
                    'user_id'     => $userId,
                    'reel_id'     => $data['reel_id'],
                    'action_type' => 'block',
                ]);

                return response()->json([
                    'message' => 'User blocked successfully.',

                ], Response::HTTP_CREATED);

            case 'unblock':
                $block = ReelUserAction::where('user_id', $userId)
                    ->where('reel_id', $data['reel_id'])
                    ->where('action_type', 'block')
                    ->first();

                if ($block) {
                    $block->delete();

                    return response()->json([
                        'message' => 'User unblocked successfully.',

                    ], Response::HTTP_OK);
                }

                return response()->json([
                    'message' => 'No block action found to remove.',

                ], Response::HTTP_NOT_FOUND);

            case 'like':
                $existingLike = ReelUserAction::where('user_id', $userId)
                    ->where('reel_id', $data['reel_id'])
                    ->where('action_type', 'like')
                    ->first();

                if (! $existingLike) {
                    ReelUserAction::create([
                        'user_id'     => $userId,
                        'reel_id'     => $data['reel_id'],
                        'action_type' => 'like',
                    ]);
                }

                return response()->json([
                    'message' => 'Reel liked successfully.',

                ], Response::HTTP_OK);

            case 'unlike':
                $existingLike = ReelUserAction::where('user_id', $userId)
                    ->where('reel_id', $data['reel_id'])
                    ->where('action_type', 'like')
                    ->first();

                if ($existingLike) {
                    $existingLike->delete();
                }

                return response()->json([
                    'message' => 'Reel unliked successfully.',

                ], Response::HTTP_OK);

            case 'follow':
                $reel = Reel::find($data['reel_id']);
                if (! $reel) {
                    return response()->json([
                        'message' => 'Reel not found.',

                    ], Response::HTTP_NOT_FOUND);
                }

                if (! MerchantFollower::where('user_id', $userId)
                    ->where('merchant_id', $reel->merchant_id)
                    ->exists()) {
                    MerchantFollower::create([
                        'user_id'     => $userId,
                        'merchant_id' => $reel->merchant_id,
                    ]);

                    return response()->json([
                        'message' => 'Shop followed successfully.',

                    ], Response::HTTP_CREATED);
                }

                return response()->json([
                    'message' => 'Shop already followed.',

                ], Response::HTTP_OK);

            case 'unfollow':
                $reel = Reel::find($data['reel_id']);
                if (! $reel) {
                    return response()->json([
                        'message' => 'Reel not found.',

                    ], Response::HTTP_NOT_FOUND);
                }

                $follow = MerchantFollower::where('user_id', $userId)
                    ->where('merchant_id', $reel->merchant_id)
                    ->first();

                if ($follow) {
                    $follow->delete();

                    return response()->json([
                        'message' => 'Shop unfollowed successfully.',

                    ], Response::HTTP_OK);
                }

                return response()->json([
                    'message' => 'Shop already unfollowed.',

                ], Response::HTTP_OK);

            default:
                return response()->json([
                    'message' => 'Invalid action type.',

                ], Response::HTTP_BAD_REQUEST);
        }
    }
}
