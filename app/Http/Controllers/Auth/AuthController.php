<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password_hash' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('api-token')->accessToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password_hash)) {
            return response()->json([
                'message' => 'Credenciais inválidas.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('api-token')->accessToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], Response::HTTP_OK);
    }
}
