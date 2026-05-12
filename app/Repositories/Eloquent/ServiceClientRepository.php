<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\ServiceClientRepositoryInterface;
use App\Models\ServiceClient;

class ServiceClientRepository implements ServiceClientRepositoryInterface
{
    public function find(int $id): ?ServiceClient
    {
        return ServiceClient::find($id);
    }

    public function paginate(array $filters = [], int $perPage = 15)
    {
        $query = ServiceClient::query();
        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%")
                ->orWhere('slug', 'like', "%{$filters['search']}%");
        }
        return $query->paginate($perPage);
    }

    public function create(array $data): ServiceClient
    {
        return ServiceClient::create($data);
    }

    public function update(ServiceClient $model, array $data): ServiceClient
    {
        $model->update($data);
        return $model;
    }

    public function delete(ServiceClient $model): bool
    {
        return $model->delete();
    }
}
