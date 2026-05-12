<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $super = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $adminAk = Role::firstOrCreate(['name' => 'admin_akademik'], ['display_name' => 'Admin Akademik']);

        $permissions = collect([
            ['name' => 'view-users', 'display_name' => 'View Users'],
            ['name' => 'create-users', 'display_name' => 'Create Users'],
            ['name' => 'update-users', 'display_name' => 'Update Users'],
            ['name' => 'delete-users', 'display_name' => 'Delete Users'],
            ['name' => 'view-roles', 'display_name' => 'View Roles'],
            ['name' => 'view-permissions', 'display_name' => 'View Permissions'],
            ['name' => 'manage-service-clients', 'display_name' => 'Manage Service Clients'],
            ['name' => 'manage-api-clients', 'display_name' => 'Manage API Clients'],
            ['name' => 'manage-roles', 'display_name' => 'Manage Roles'],
            ['name' => 'manage-permissions', 'display_name' => 'Manage Permissions'],
        ])->map(function (array $permissionData) {
            return Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                ['display_name' => $permissionData['display_name']]
            );
        });

        $super->permissions()->sync($permissions->pluck('id')->all());
        $adminAk->permissions()->sync([
            $permissions->firstWhere('name', 'view-users')->id,
            $permissions->firstWhere('name', 'view-roles')->id,
            $permissions->firstWhere('name', 'view-permissions')->id,
        ]);

        $superUser = User::firstOrCreate(['email' => 'admin@poliban.ac.id'], [
            'name' => 'Super Admin',
            'password' => bcrypt('password123'),
        ]);
        $superUser->roles()->syncWithoutDetaching([$super->id]);

        $akademikUser = User::firstOrCreate(['email' => 'admin.akademik@poliban.ac.id'], [
            'name' => 'Admin Akademik',
            'password' => bcrypt('password123'),
        ]);
        $akademikUser->roles()->syncWithoutDetaching([$adminAk->id]);
    }
}
