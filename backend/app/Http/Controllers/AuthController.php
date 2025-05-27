<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            if (!Auth::guard('web')->attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Invalid credentials',
                    'data' => null,
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                ]
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $err->getMessage(),
            ], 500);
        }
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            $user->tokens()->delete(); // Revoke all tokens for this user

            return response()->json([
                'message' => 'Logout successfully',
                'data' => null,
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $err->getMessage(),
            ], 500);
        }
    }

    public function getUser()
    {
        try {
            $user = Auth::user();

            return response()->json([
                'message' => 'User retrieved successtully',
                'data' => new UserResource($user),
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => 'Failed to retrieve user',
                'error' => $err->getMessage(),
            ], 500);
        }
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password'])
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'message' => 'Registration successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                ]
            ], 201);
        } catch (\Exception $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registration failed',
                'error' => $err->getMessage(),
            ], 500);
        }
    }
}
