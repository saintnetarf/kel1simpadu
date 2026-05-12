<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;

/**
 * @group Authentication
 *
 * Auth Center endpoints for login, token validation, profile, logout, and refresh token flow.
 */
class AuthController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    /**
     * Authenticate a user and issue a bearer token.
     *
     * @example {"success":true,"message":"Login berhasil","data":{"token":"eyJ...","user":{"id":1,"name":"Super Admin","email":"admin@poliban.ac.id"},"roles":["super_admin"]}}
     */
    public function login(LoginRequest $request)
    {
        $res = $this->authService->login($request->validated());
        if (!$res['success']) {
            return response()->json(['success' => false, 'message' => $res['message']], 401);
        }
        $data = $res['data'];
        return response()->json(['success' => true, 'message' => 'Login berhasil', 'data' => [
            'token' => $data['token'],
            'user' => new UserResource($data['user']),
            'roles' => $data['roles'],
        ]]);
    }

    /**
     * Revoke the current bearer token.
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request);
        return response()->json(['success' => true, 'message' => 'Logout berhasil']);
    }

    /**
     * Rotate the current bearer token and return a new token.
     */
    public function refreshToken(Request $request)
    {
        $token = $request->bearerToken() ?? $request->input('token');
        $res = $this->authService->refreshToken($token);

        if (!$res['success']) {
            return response()->json(['success' => false, 'message' => $res['message']], 401);
        }

        $data = $res['data'];

        return response()->json([
            'success' => true,
            'message' => 'Token berhasil diperbarui',
            'data' => [
                'token' => $data['token'],
                'user' => new UserResource($data['user']),
                'roles' => $data['roles'],
            ],
        ]);
    }

    /**
     * Validate a bearer token for other microservices.
     */
    public function validateToken(Request $request)
    {
        $token = $request->input('token') ?? $request->bearerToken();
        $valid = $this->authService->validateToken($token);
        return response()->json(['success' => $valid, 'message' => $valid ? 'Token valid' : 'Token tidak valid']);
    }

    /**
     * Get the authenticated user profile and assigned roles.
     */
    public function me(Request $request)
    {
        $user = $this->authService->resolveUserByToken($request->bearerToken());
        if (!$user) return response()->json(['success'=>false,'message'=>'Unauthorized'],401);
        return response()->json(['success' => true, 'message' => 'OK', 'data' => ['user' => new UserResource($user), 'roles' => $user->roles->pluck('name')]]);
    }
}
