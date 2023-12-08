<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $tokenResult = $user->createNewAccessToken();
        return response()->json([
            'message'    => 'Successfully registered!',
            'auth_token' => $tokenResult->plainTextToken,

        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = request(['email', 'password']);
        abort_if(!Auth::attempt($credentials), 401, "Unauthorized");

        $user = $request->user();
        $accessToken = $request->user()->currentAccessToken();
        if(!$accessToken){
            $accessToken = $user->createNewAccessToken();
        }

        return response()->json([
            'message'    => "Logged in successfully. Welcome {$user->name}",
            'token_type' => 'Bearer',
            'auth_token' => $accessToken->plainTextToken,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
