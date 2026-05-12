<?php

namespace Tests\Unit\Policies;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Pegawai;
use Database\Seeders\RoleSeeder;
use Database\Seeders\PegawaiPermissionSeeder;
use Database\Seeders\PegawaiSeeder;

class PegawaiPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdminUser;
    private User $adminAkademikUser;
    private User $regularUser;
    private Pegawai $pegawai;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(PegawaiPermissionSeeder::class);
        $this->seed(PegawaiSeeder::class);

        $super = Role::firstWhere('name', 'super_admin');
        $adminAk = Role::firstWhere('name', 'admin_akademik');

        $this->superAdminUser = User::factory()->create(['email' => 'super@poliban.ac.id']);
        $this->superAdminUser->roles()->attach($super->id);

        $this->adminAkademikUser = User::factory()->create(['email' => 'admin.ak@poliban.ac.id']);
        $this->adminAkademikUser->roles()->attach($adminAk->id);

        $this->regularUser = User::factory()->create(['email' => 'regular@poliban.ac.id']);

        $this->pegawai = Pegawai::first();
    }

    public function test_super_admin_can_view_any_pegawai()
    {
        $this->assertTrue($this->superAdminUser->can('viewAny', Pegawai::class));
    }

    public function test_admin_akademik_can_view_any_pegawai()
    {
        $this->assertTrue($this->adminAkademikUser->can('viewAny', Pegawai::class));
    }

    public function test_regular_user_cannot_view_any_pegawai()
    {
        $this->assertFalse($this->regularUser->can('viewAny', Pegawai::class));
    }

    public function test_super_admin_can_view_single_pegawai()
    {
        $this->assertTrue($this->superAdminUser->can('view', $this->pegawai));
    }

    public function test_admin_akademik_can_view_single_pegawai()
    {
        $this->assertTrue($this->adminAkademikUser->can('view', $this->pegawai));
    }

    public function test_regular_user_cannot_view_single_pegawai()
    {
        $this->assertFalse($this->regularUser->can('view', $this->pegawai));
    }

    public function test_super_admin_can_create_pegawai()
    {
        $this->assertTrue($this->superAdminUser->can('create', Pegawai::class));
    }

    public function test_admin_akademik_cannot_create_pegawai()
    {
        $this->assertFalse($this->adminAkademikUser->can('create', Pegawai::class));
    }

    public function test_regular_user_cannot_create_pegawai()
    {
        $this->assertFalse($this->regularUser->can('create', Pegawai::class));
    }

    public function test_super_admin_can_update_pegawai()
    {
        $this->assertTrue($this->superAdminUser->can('update', $this->pegawai));
    }

    public function test_admin_akademik_cannot_update_pegawai()
    {
        $this->assertFalse($this->adminAkademikUser->can('update', $this->pegawai));
    }

    public function test_regular_user_cannot_update_pegawai()
    {
        $this->assertFalse($this->regularUser->can('update', $this->pegawai));
    }

    public function test_super_admin_can_delete_pegawai()
    {
        $this->assertTrue($this->superAdminUser->can('delete', $this->pegawai));
    }

    public function test_admin_akademik_cannot_delete_pegawai()
    {
        $this->assertFalse($this->adminAkademikUser->can('delete', $this->pegawai));
    }

    public function test_regular_user_cannot_delete_pegawai()
    {
        $this->assertFalse($this->regularUser->can('delete', $this->pegawai));
    }
}
