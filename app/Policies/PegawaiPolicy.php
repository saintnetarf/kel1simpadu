<?php

namespace App\Policies;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PegawaiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-pegawai');
    }

    public function view(User $user, Pegawai $pegawai): bool
    {
        return $user->hasPermission('view-pegawai');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create-pegawai');
    }

    public function update(User $user, Pegawai $pegawai): bool
    {
        return $user->hasPermission('update-pegawai');
    }

    public function delete(User $user, Pegawai $pegawai): bool
    {
        return $user->hasPermission('delete-pegawai');
    }
}
