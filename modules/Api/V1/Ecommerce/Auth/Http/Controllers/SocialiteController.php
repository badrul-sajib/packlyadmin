<?php

namespace Modules\Api\V1\Ecommerce\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User\SocketToken;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class SocialiteController extends Controller
{
    public function redirect($provider)
    {
        if (! in_array($provider, ['facebook', 'google'])) {
            return response()->json(['message' => 'Invalid provider'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to generate redirect URL'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function callback($provider)
    {
        if (! in_array($provider, ['facebook', 'google'])) {
            return response()->json(['message' => 'Invalid provider'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            $user = User::where('email', $socialUser->email)->first();

            if (! $user) {
                $user = User::create([
                    'name'          => $socialUser->name ?? ($socialUser->email ?? 'Social User'),
                    'email'         => $socialUser->email,
                    $provider.'_id' => $socialUser->id,
                    'password'      => Hash::make(Str::random(16)),
                ]);
            } else {
                if (! $user->{$provider.'_id'}) {
                    $user->update([$provider.'_id' => $socialUser->id]);
                }
            }

            $tokenResult = $user->createToken('authToken');
            $token       = $tokenResult->plainTextToken;

            $socketToken = $this->generateSocketToken();

            SocketToken::create([
                'user_id'                  => $user->id,
                'personal_access_token_id' => $tokenResult->accessToken->id,
                'token'                    => $socketToken,
            ]);

            return response()->json([
                'token' => $token,
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
                'socket_token' => $socketToken,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Social login failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function generateSocketToken()
    {
        $uniqueId = Str::random(32);

        if (SocketToken::where('token', $uniqueId)->exists()) {
            return $this->generateSocketToken();
        }

        return $uniqueId;
    }
}
