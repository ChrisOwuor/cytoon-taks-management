<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\loginUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authcontroller extends Controller
{

    public function login(loginUserRequest $request)
    {
        // Login logic goes here

        $data = $request->validated();

        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $token = Auth::attempt($credentials);

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
        ], Response::HTTP_OK);
    }
}
