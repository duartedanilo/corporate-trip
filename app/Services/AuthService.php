<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(private UserRepository $repository) {}

    public function register(array $data)
    {
        $user = $this->repository->create($data);
        $token = JWTAuth::claims(['is_admin' => $user->is_admin])->fromUser($user);

        return compact('user', 'token');
    }

    public function login(array $credentials): string
    {
        if (!JWTAuth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'credentials' => 'Invalid credentials.'
            ]);
        }

        $user = auth()->user();
        return JWTAuth::claims(['is_admin' => $user->is_admin])->fromUser($user);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Logged out successfully']);
    }
}
