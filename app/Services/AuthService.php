<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\ApiToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;

class AuthService
{
    public function __construct(protected UserRepositoryInterface $users)
    {
    }

    public function login(array $credentials): array
    {
        $user = $this->users->findByEmail($credentials['email']);
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        return $this->issueTokenForUser($user, 'auth-center-token');
    }

    public function refreshToken(?string $plainToken): array
    {
        $apiToken = $this->findTokenByPlainText($plainToken);
        if (!$apiToken || !$apiToken->user) {
            return ['success' => false, 'message' => 'Token tidak valid'];
        }

        $user = $apiToken->user;
        $apiToken->delete();

        return $this->issueTokenForUser($user, $apiToken->name ?: 'auth-center-token');
    }

    public function logout(Request $request): void
    {
        $token = $request->bearerToken();
        $apiToken = $this->findTokenByPlainText($token);
        if ($apiToken) {
            $apiToken->delete();
        }
    }

    public function validateToken(?string $token): bool
    {
        return (bool) $this->findTokenByPlainText($token);
    }

    public function resolveUserByToken(?string $token): ?User
    {
        $apiToken = $this->findTokenByPlainText($token);
        if (!$apiToken) {
            return null;
        }

        $apiToken->forceFill(['last_used_at' => now()])->save();

        return $apiToken->user?->load('roles');
    }

    private function issueTokenForUser(User $user, string $name): array
    {
        $plainToken = Str::random(80);
        ApiToken::create([
            'user_id' => $user->id,
            'name' => $name,
            'token' => $this->hashToken($plainToken),
            'abilities' => ['*'],
            'last_used_at' => now(),
        ]);

        return [
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $plainToken,
                'user' => $user->load('roles'),
                'roles' => $user->roles->pluck('name'),
            ],
        ];
    }

    private function findTokenByPlainText(?string $plainToken): ?ApiToken
    {
        if (!$plainToken) {
            return null;
        }

        return ApiToken::with('user')->where('token', $this->hashToken($plainToken))->first();
    }

    private function hashToken(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }
}
