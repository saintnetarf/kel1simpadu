<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Database\Seeders\RoleSeeder;
use Database\Seeders\AcademicMasterSeeder;
use Database\Seeders\PegawaiSeeder;

class PegawaiInClassParticipantFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_pegawai_dropdown_present_in_class_participant_form_for_super_admin()
    {
        // seed roles and basic data
        $this->seed(RoleSeeder::class);
        $this->seed(AcademicMasterSeeder::class);
        $this->seed(PegawaiSeeder::class);

        $super = Role::firstWhere('name', 'super_admin');

        $user = User::factory()->create(['email' => 'test.super@poliban.ac.id']);
        $user->roles()->attach($super->id);

        $response = $this->actingAs($user)->get(route('master-data.class-participants.create'));

        $response->assertStatus(200);
        $response->assertSee('name="pegawai_id"', false);
    }
}
