<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;


class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user_data = $request->validated();
        $user_data['email_verified_at'] = now();

        $user = User::create($user_data);

        $tokenResult = $user->createToken('auth_token');
        $token = $tokenResult->accessToken ?? $tokenResult->token?->id ?? null;

        $user['token'] = [
            'access_token' => $tokenResult->accessToken ?? null,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token?->expires_at ? Carbon::parse($tokenResult->token->expires_at)->toDateTimeString() : null,
        ];

        return response()->json([
            'success' => true,
            'status_code' => 201,
            'data' => $user,
            'message' => 'User has been registered successfully.'
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            $tokenResult = $user->createToken('auth_token');

            $user['token'] = [
                'access_token' => $tokenResult->accessToken ?? null,
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult->token?->expires_at ? Carbon::parse($tokenResult->token->expires_at)->toDateTimeString() : null,
            ];

            return response()->json([
                'success' => true,
                'status_code' => 200,
                'data' => $user,
                'message' => 'User logged in successfully.'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'Invalid email or password.'
            ], 401);
        }
    }

    public function detail()
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'status_code' => 200,
            'data' => $user,
            'message' => 'User details fetched successfully.'
        ], 200);
    }

    public function logout()
    {
        Auth::user()->token()->delete();

        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => 'User logged out successfully.'
        ], 200);
    }
}