<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function __construct(private AuthService $service) {}

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $token = $this->service->register($validated);

        return response()->json($token, 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        try {
            $token = $this->service->login($credentials);
            return response()->json(compact('token'));
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token', 'message' => $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        $this->service->logout();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
