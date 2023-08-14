<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Enum\ResponseCodes;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'These credentials do not match our records.',
                    'status' => ResponseCodes::UNAUTHORIZED
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'status' => ResponseCodes::SOMETHING_WENT_WRONG
            ]);
        }

        $success = [
            'token' => $token,
            'user' => JWTAuth::setToken($token)->authenticate()

        ];

        return response()->json([
            'success' => true,
            'message' => 'Authenticated',
            'data' => $success,
            'status' => ResponseCodes::HTTP_OK
        ]);
    }
}