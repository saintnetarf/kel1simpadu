<?php

namespace App\Interfaces;

use App\Models\ServiceClient;

interface ServiceClientRepositoryInterface
{
    public function find(int $id): ?ServiceClient;
    public function paginate(array $filters = [], int $perPage = 15);
    public function create(array $data): ServiceClient;
    public function update(ServiceClient $model, array $data): ServiceClient;
    public function delete(ServiceClient $model): bool;
}
