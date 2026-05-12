<?php

namespace App\Interfaces;

use App\Models\ApiToken;

interface ApiTokenRepositoryInterface
{
    public function find(int $id): ?ApiToken;
    public function findByToken(string $token): ?ApiToken;
    public function paginate(array $filters = [], int $perPage = 15);
    public function create(array $data): ApiToken;
    public function delete(ApiToken $model): bool;
}
