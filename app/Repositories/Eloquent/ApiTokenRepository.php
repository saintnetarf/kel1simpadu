<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\ApiTokenRepositoryInterface;
use App\Models\ApiToken;

class ApiTokenRepository implements ApiTokenRepositoryInterface
{
    public function find(int $id): ?ApiToken
    {
        return ApiToken::with('user')->find($id);
    }

    public function findByToken(string $token): ?ApiToken
    {
        return ApiToken::with('user')->where('token', hash('sha256', $token))->first();
    }

    public function paginate(array $filters = [], int $perPage = 15)
    {
        $query = ApiToken::with('user');
        if (!empty($filters['user_id'])) $query->where('user_id', $filters['user_id']);
        return $query->paginate($perPage);
    }

    public function create(array $data): ApiToken
    {
        return ApiToken::create($data);
    }

    public function delete(ApiToken $model): bool
    {
        return $model->delete();
    }
}
