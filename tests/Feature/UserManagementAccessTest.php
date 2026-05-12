<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementAccessTest extends TestCase
{
    use RefreshDatabase;

    private const TARGET_USER_EMAIL = 'target.user@example.com';

    public function test_super_admin_can_crud_users_from_web_user_management(): void
    {
        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Administrator',
        ]);
        $adminAkademikRole = Role::create([
            'name' => 'admin_akademik',
            'display_name' => 'Admin Akademik',
        ]);

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->sync([$superAdminRole->id]);

        $this->actingAs($superAdmin)
            ->get(route('users.index'))
            ->assertOk();

        $this->actingAs($superAdmin)
            ->get(route('users.create'))
            ->assertOk();

        $storePayload = [
            'name' => 'Test User Created',
            'email' => 'test.created@example.com',
            'password' => 'password123',
            'roles' => [$adminAkademikRole->id],
        ];

        $this->actingAs($superAdmin)
            ->post(route('users.store'), $storePayload)
            ->assertRedirect(route('users.index'));

        $createdUser = User::where('email', 'test.created@example.com')->firstOrFail();

        $this->assertDatabaseHas('user_roles', [
            'user_id' => $createdUser->id,
            'role_id' => $adminAkademikRole->id,
        ]);

        $this->actingAs($superAdmin)
            ->get(route('users.edit', $createdUser))
            ->assertOk();

        $this->actingAs($superAdmin)
            ->put(route('users.update', $createdUser), [
                'name' => 'Test User Updated',
                'email' => 'test.updated@example.com',
                'password' => '',
                'roles' => [$superAdminRole->id],
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $createdUser->id,
            'name' => 'Test User Updated',
            'email' => 'test.updated@example.com',
        ]);

        $this->assertDatabaseHas('user_roles', [
            'user_id' => $createdUser->id,
            'role_id' => $superAdminRole->id,
        ]);

        $this->actingAs($superAdmin)
            ->delete(route('users.destroy', $createdUser))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $createdUser->id,
        ]);
    }

    public function test_admin_akademik_is_read_only_for_web_user_management(): void
    {
        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Administrator',
        ]);
        $adminAkademikRole = Role::create([
            'name' => 'admin_akademik',
            'display_name' => 'Admin Akademik',
        ]);

        $adminAkademik = User::factory()->create();
        $adminAkademik->roles()->sync([$adminAkademikRole->id]);

        $targetUser = User::factory()->create([
            'email' => self::TARGET_USER_EMAIL,
        ]);
        $targetUser->roles()->sync([$superAdminRole->id]);

        $this->actingAs($adminAkademik)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSeeText('Read Only');

        $this->actingAs($adminAkademik)
            ->get(route('users.create'))
            ->assertForbidden();

        $this->actingAs($adminAkademik)
            ->post(route('users.store'), [
                'name' => 'Should Not Create',
                'email' => 'should.not.create@example.com',
                'password' => 'password123',
                'roles' => [$adminAkademikRole->id],
            ])
            ->assertForbidden();

        $this->actingAs($adminAkademik)
            ->get(route('users.edit', $targetUser))
            ->assertForbidden();

        $this->actingAs($adminAkademik)
            ->put(route('users.update', $targetUser), [
                'name' => 'Should Not Update',
                'email' => self::TARGET_USER_EMAIL,
                'password' => '',
                'roles' => [$adminAkademikRole->id],
            ])
            ->assertForbidden();

        $this->actingAs($adminAkademik)
            ->delete(route('users.destroy', $targetUser))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'email' => self::TARGET_USER_EMAIL,
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'should.not.create@example.com',
        ]);
    }
}
