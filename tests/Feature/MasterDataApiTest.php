<?php

namespace Tests\Feature;

use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\ClassParticipant;
use App\Models\Course;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataApiTest extends TestCase
{
    use RefreshDatabase;

    private const STUDY_PROGRAM_CODE = 'SI';
    private const STUDY_PROGRAM_NAME = 'Sistem Informasi';
    private const STUDY_PROGRAM_UPDATED_NAME = 'Sistem Informasi Updated';
    private const ACADEMIC_YEAR_NAME = '2025/2026';
    private const STUDENT_NUMBER = '2025001';

    public function test_super_admin_can_crud_academic_years_via_api(): void
    {
        $token = $this->loginSuperAdmin();

        $this->withToken($token)
            ->getJson('/api/academic-years')
            ->assertOk();

        $this->withToken($token)
            ->postJson('/api/academic-years', [
                'name' => self::ACADEMIC_YEAR_NAME,
                'start_year' => 2025,
                'end_year' => 2026,
                'is_active' => true,
                'description' => 'Tahun akademik aktif',
            ])
            ->assertCreated();

        $academicYear = AcademicYear::where('name', self::ACADEMIC_YEAR_NAME)->firstOrFail();

        $this->withToken($token)
            ->putJson('/api/academic-years/' . $academicYear->id, [
                'name' => '2025/2026 Genap',
                'start_year' => 2025,
                'end_year' => 2026,
                'is_active' => false,
                'description' => 'Diarsipkan',
            ])
            ->assertOk();

        $this->assertDatabaseHas('academic_years', [
            'id' => $academicYear->id,
            'name' => '2025/2026 Genap',
            'is_active' => 0,
        ]);

        $this->withToken($token)
            ->deleteJson('/api/academic-years/' . $academicYear->id)
            ->assertOk();

        $this->assertDatabaseMissing('academic_years', [
            'id' => $academicYear->id,
        ]);
    }

    public function test_super_admin_can_crud_study_programs_and_courses_via_api(): void
    {
        $token = $this->loginSuperAdmin();

        $this->withToken($token)
            ->postJson('/api/study-programs', [
                'code' => self::STUDY_PROGRAM_CODE,
                'name' => self::STUDY_PROGRAM_NAME,
                'degree_level' => 'S1',
                'description' => 'Program studi',
                'is_active' => true,
            ])
            ->assertCreated();

        $studyProgram = StudyProgram::where('code', self::STUDY_PROGRAM_CODE)->firstOrFail();

        $this->withToken($token)
            ->putJson('/api/study-programs/' . $studyProgram->id, [
                'code' => self::STUDY_PROGRAM_CODE,
                'name' => self::STUDY_PROGRAM_UPDATED_NAME,
                'degree_level' => 'S1',
                'description' => 'Updated',
                'is_active' => false,
            ])
            ->assertOk();

        $this->assertDatabaseHas('study_programs', [
            'id' => $studyProgram->id,
            'name' => self::STUDY_PROGRAM_UPDATED_NAME,
            'is_active' => 0,
        ]);

        $this->withToken($token)
            ->postJson('/api/courses', [
                'study_program_id' => $studyProgram->id,
                'code' => 'SI101',
                'name' => 'Dasar Sistem Informasi',
                'credits' => 3,
                'semester' => 1,
                'description' => 'Dasar',
                'is_active' => true,
            ])
            ->assertCreated();

        $course = Course::where('code', 'SI101')->firstOrFail();

        $this->withToken($token)
            ->putJson('/api/courses/' . $course->id, [
                'study_program_id' => $studyProgram->id,
                'code' => 'SI101',
                'name' => 'Dasar Sistem Informasi Updated',
                'credits' => 4,
                'semester' => 2,
                'description' => 'Updated',
                'is_active' => false,
            ])
            ->assertOk();

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Dasar Sistem Informasi Updated',
            'credits' => 4,
            'is_active' => 0,
        ]);

        $this->withToken($token)
            ->deleteJson('/api/courses/' . $course->id)
            ->assertOk();

        $this->assertDatabaseMissing('courses', [
            'id' => $course->id,
        ]);

        $this->withToken($token)
            ->deleteJson('/api/study-programs/' . $studyProgram->id)
            ->assertOk();
    }

    public function test_super_admin_can_crud_classes_and_students_via_api(): void
    {
        $token = $this->loginSuperAdmin();

        $studyProgram = StudyProgram::create([
            'code' => self::STUDY_PROGRAM_CODE,
            'name' => self::STUDY_PROGRAM_NAME,
            'degree_level' => 'S1',
            'is_active' => true,
        ]);

        $this->withToken($token)
            ->postJson('/api/classes', [
                'study_program_id' => $studyProgram->id,
                'name' => 'SI-1A',
                'level' => 1,
                'description' => 'Kelas awal',
                'is_active' => true,
            ])
            ->assertCreated();

        $class = AcademicClass::where('name', 'SI-1A')->firstOrFail();

        $this->withToken($token)
            ->putJson('/api/classes/' . $class->id, [
                'study_program_id' => $studyProgram->id,
                'name' => 'SI-1B',
                'level' => 2,
                'description' => 'Kelas update',
                'is_active' => false,
            ])
            ->assertOk();

        $this->assertDatabaseHas('classes', [
            'id' => $class->id,
            'name' => 'SI-1B',
            'is_active' => 0,
        ]);

        $this->withToken($token)
            ->postJson('/api/students', [
                'study_program_id' => $studyProgram->id,
                'student_number' => self::STUDENT_NUMBER,
                'name' => 'Budi',
                'email' => 'budi@example.com',
                'phone' => '08123456789',
                'address' => 'Bandung',
                'is_active' => true,
            ])
            ->assertCreated();

        $student = Student::where('student_number', self::STUDENT_NUMBER)->firstOrFail();

        $this->withToken($token)
            ->putJson('/api/students/' . $student->id, [
                'study_program_id' => $studyProgram->id,
                'student_number' => self::STUDENT_NUMBER,
                'name' => 'Budi Updated',
                'email' => 'budi.updated@example.com',
                'phone' => '08123456780',
                'address' => 'Jakarta',
                'is_active' => false,
            ])
            ->assertOk();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Budi Updated',
            'is_active' => 0,
        ]);

        $this->withToken($token)
            ->deleteJson('/api/students/' . $student->id)
            ->assertOk();

        $this->assertDatabaseMissing('students', [
            'id' => $student->id,
        ]);

        $this->withToken($token)
            ->deleteJson('/api/classes/' . $class->id)
            ->assertOk();
    }

    public function test_super_admin_can_crud_class_participants_via_api(): void
    {
        $token = $this->loginSuperAdmin();

        $academicYear = AcademicYear::create([
            'name' => self::ACADEMIC_YEAR_NAME,
            'start_year' => 2025,
            'end_year' => 2026,
            'is_active' => true,
        ]);

        $studyProgram = StudyProgram::create([
            'code' => self::STUDY_PROGRAM_CODE,
            'name' => self::STUDY_PROGRAM_NAME,
            'degree_level' => 'S1',
            'is_active' => true,
        ]);

        $class = AcademicClass::create([
            'study_program_id' => $studyProgram->id,
            'name' => 'SI-1A',
            'level' => 1,
            'is_active' => true,
        ]);

        $course = Course::create([
            'study_program_id' => $studyProgram->id,
            'code' => 'SI101',
            'name' => 'Dasar Sistem Informasi',
            'credits' => 3,
            'semester' => 1,
            'is_active' => true,
        ]);

        $student = Student::create([
            'study_program_id' => $studyProgram->id,
            'student_number' => self::STUDENT_NUMBER,
            'name' => 'Budi',
            'is_active' => true,
        ]);

        $this->withToken($token)
            ->postJson('/api/class-participants', [
                'academic_year_id' => $academicYear->id,
                'study_program_id' => $studyProgram->id,
                'class_id' => $class->id,
                'course_id' => $course->id,
                'student_id' => $student->id,
                'participant_status' => 1,
                'notes' => 'Peserta aktif',
            ])
            ->assertCreated();

        $participant = ClassParticipant::firstOrFail();

        $this->withToken($token)
            ->putJson('/api/class-participants/' . $participant->id, [
                'academic_year_id' => $academicYear->id,
                'study_program_id' => $studyProgram->id,
                'class_id' => $class->id,
                'course_id' => $course->id,
                'student_id' => $student->id,
                'participant_status' => 2,
                'notes' => 'Keluar',
            ])
            ->assertOk();

        $this->assertDatabaseHas('class_participants', [
            'id' => $participant->id,
            'participant_status' => 2,
        ]);

        $this->withToken($token)
            ->deleteJson('/api/class-participants/' . $participant->id)
            ->assertOk();

        $this->assertDatabaseMissing('class_participants', [
            'id' => $participant->id,
        ]);
    }

    private function loginSuperAdmin(): string
    {
        $role = Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Administrator',
        ]);

        $user = User::factory()->create([
            'email' => 'admin@poliban.ac.id',
            'password' => bcrypt('password123'),
        ]);
        $user->roles()->sync([$role->id]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@poliban.ac.id',
            'password' => 'password123',
        ]);

        $response->assertOk();

        return (string) data_get($response->json(), 'data.token');
    }

}
