<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class GoogleAuthController extends Controller
{
    
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                ]);
            }

            $token = JWTAuth::fromUser($user);

            return response()->success(['token' => $token,'user' => $user ], 'Access granted!');

       } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to authenticate with Google. Please try again.',
            ], 500);
        }
    }
 
    public function logout(Request $request)
    {
        return app('App\Http\Controllers\Api\AuthController')->logout($request);
    }
}
