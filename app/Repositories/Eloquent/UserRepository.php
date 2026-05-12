<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function find(int $id): ?User
    {
        return User::with('roles')->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function paginate(array $filters = [], int $perPage = 15)
    {
        $query = User::query()->with('roles');
        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
        }
        return $query->paginate($perPage);
    }

    public function create(array $data): User
    {
        $user = User::create($data);
        if (!empty($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }
        return $user->load('roles');
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        if (isset($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }
        return $user->load('roles');
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
