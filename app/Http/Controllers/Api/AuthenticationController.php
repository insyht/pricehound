<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate(
                [
                    'email' => 'required|email',
                    'password' => 'required|string',
                ]
            );

            if (!Auth::attempt($credentials)) {
                return response()->json(
                    [
                        'response_code' => 401,
                        'status' => 'error',
                        'message' => 'Unauthorized',
                    ],
                    401
                );
            }

            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(
                [
                    'response_code' => 200,
                    'status' => 'success',
                    'message' => 'Login successful',
                    'user_info' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                    ]
            );
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'response_code' => 422,
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ],
                422
            );
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());

            return response()->json(
                [
                    'response_code' => 500,
                    'status' => 'error',
                    'message' => 'Login failed',
                    ],
                500
            );
        }
    }
}
