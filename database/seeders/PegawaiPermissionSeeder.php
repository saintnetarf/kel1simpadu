<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PegawaiPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            ['name' => 'view-pegawai', 'display_name' => 'View Pegawai'],
            ['name' => 'create-pegawai', 'display_name' => 'Create Pegawai'],
            ['name' => 'update-pegawai', 'display_name' => 'Update Pegawai'],
            ['name' => 'delete-pegawai', 'display_name' => 'Delete Pegawai'],
        ];

        $permissions = collect($perms)->map(function ($p) {
            return Permission::firstOrCreate(['name' => $p['name']], ['display_name' => $p['display_name']]);
        });

        $super = Role::firstWhere('name', 'super_admin');
        $adminAk = Role::firstWhere('name', 'admin_akademik');

        if ($super) {
            $super->permissions()->syncWithoutDetaching($permissions->pluck('id')->all());
        }

        if ($adminAk) {
            // admin akademik can view pegawai but not manage
            $view = $permissions->firstWhere('name', 'view-pegawai');
            if ($view) {
                $adminAk->permissions()->syncWithoutDetaching([$view->id]);
            }
        }
    }
}
