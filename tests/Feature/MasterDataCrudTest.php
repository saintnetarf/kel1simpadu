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

class MasterDataCrudTest extends TestCase
{
    use RefreshDatabase;

    private const STUDY_PROGRAM_CODE = 'TI';
    private const STUDY_PROGRAM_NAME = 'Teknik Informatika';
    private const STUDY_PROGRAM_UPDATED_NAME = 'Teknik Informatika Updated';
    private const ACADEMIC_YEAR_NAME = '2025/2026';
    private const STUDENT_NUMBER = '2025001';

    public function test_super_admin_can_crud_study_programs(): void
    {
        $superAdmin = $this->createSuperAdmin();

        $this->actingAs($superAdmin)
            ->get(route('master-data.study-programs.index'))
            ->assertOk();

        $this->actingAs($superAdmin)
            ->post(route('master-data.study-programs.store'), [
                'code' => self::STUDY_PROGRAM_CODE,
                'name' => self::STUDY_PROGRAM_NAME,
                'degree_level' => 'S1',
                'description' => 'Program studi informatika',
                'is_active' => 1,
            ])
            ->assertRedirect(route('master-data.study-programs.index'));

        $studyProgram = StudyProgram::where('code', self::STUDY_PROGRAM_CODE)->firstOrFail();

        $this->actingAs($superAdmin)
            ->put(route('master-data.study-programs.update', $studyProgram), [
                'code' => self::STUDY_PROGRAM_CODE,
                'name' => self::STUDY_PROGRAM_UPDATED_NAME,
                'degree_level' => 'S1',
                'description' => 'Updated',
                'is_active' => 0,
            ])
            ->assertRedirect(route('master-data.study-programs.index'));

        $this->assertDatabaseHas('study_programs', [
            'id' => $studyProgram->id,
            'name' => self::STUDY_PROGRAM_UPDATED_NAME,
            'is_active' => 0,
        ]);

        $this->actingAs($superAdmin)
            ->delete(route('master-data.study-programs.destroy', $studyProgram))
            ->assertRedirect(route('master-data.study-programs.index'));

        $this->assertDatabaseMissing('study_programs', [
            'id' => $studyProgram->id,
        ]);
    }

    public function test_super_admin_can_crud_academic_years(): void
    {
        $superAdmin = $this->createSuperAdmin();

        $this->actingAs($superAdmin)
            ->post(route('master-data.academic-years.store'), [
                'name' => self::ACADEMIC_YEAR_NAME,
                'start_year' => 2025,
                'end_year' => 2026,
                'is_active' => 1,
                'description' => 'Tahun akademik aktif',
            ])
            ->assertRedirect(route('master-data.academic-years.index'));

        $academicYear = AcademicYear::where('name', self::ACADEMIC_YEAR_NAME)->firstOrFail();

        $this->actingAs($superAdmin)
            ->put(route('master-data.academic-years.update', $academicYear), [
                'name' => '2025/2026 Genap',
                'start_year' => 2025,
                'end_year' => 2026,
                'is_active' => 0,
                'description' => 'Diarsipkan',
            ])
            ->assertRedirect(route('master-data.academic-years.index'));

        $this->assertDatabaseHas('academic_years', [
            'id' => $academicYear->id,
            'name' => '2025/2026 Genap',
            'is_active' => 0,
        ]);

        $this->actingAs($superAdmin)
            ->delete(route('master-data.academic-years.destroy', $academicYear))
            ->assertRedirect(route('master-data.academic-years.index'));

        $this->assertDatabaseMissing('academic_years', [
            'id' => $academicYear->id,
        ]);
    }

    public function test_super_admin_can_crud_classes_and_courses(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $studyProgram = StudyProgram::create([
            'code' => 'SI',
            'name' => 'Sistem Informasi',
            'degree_level' => 'S1',
            'is_active' => 1,
        ]);

        $this->actingAs($superAdmin)
            ->post(route('master-data.classes.store'), [
                'study_program_id' => $studyProgram->id,
                'name' => 'SI-1A',
                'level' => 1,
                'description' => 'Kelas awal',
                'is_active' => 1,
            ])
            ->assertRedirect(route('master-data.classes.index'));

        $class = AcademicClass::where('name', 'SI-1A')->firstOrFail();

        $this->actingAs($superAdmin)
            ->put(route('master-data.classes.update', $class), [
                'study_program_id' => $studyProgram->id,
                'name' => 'SI-1B',
                'level' => 2,
                'description' => 'Kelas update',
                'is_active' => 0,
            ])
            ->assertRedirect(route('master-data.classes.index'));

        $this->assertDatabaseHas('classes', [
            'id' => $class->id,
            'name' => 'SI-1B',
            'is_active' => 0,
        ]);

        $this->actingAs($superAdmin)
            ->post(route('master-data.courses.store'), [
                'study_program_id' => $studyProgram->id,
                'code' => 'SI101',
                'name' => 'Dasar Sistem Informasi',
                'credits' => 3,
                'semester' => 1,
                'description' => 'Mata kuliah dasar',
                'is_active' => 1,
            ])
            ->assertRedirect(route('master-data.courses.index'));

        $course = Course::where('code', 'SI101')->firstOrFail();

        $this->actingAs($superAdmin)
            ->put(route('master-data.courses.update', $course), [
                'study_program_id' => $studyProgram->id,
                'code' => 'SI101',
                'name' => 'Dasar Sistem Informasi Updated',
                'credits' => 4,
                'semester' => 2,
                'description' => 'Updated',
                'is_active' => 0,
            ])
            ->assertRedirect(route('master-data.courses.index'));

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Dasar Sistem Informasi Updated',
            'credits' => 4,
            'is_active' => 0,
        ]);

        $this->actingAs($superAdmin)
            ->delete(route('master-data.courses.destroy', $course))
            ->assertRedirect(route('master-data.courses.index'));

        $this->assertDatabaseMissing('courses', [
            'id' => $course->id,
        ]);
    }

    public function test_super_admin_can_crud_students(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $studyProgram = StudyProgram::create([
            'code' => self::STUDY_PROGRAM_CODE,
            'name' => self::STUDY_PROGRAM_NAME,
            'degree_level' => 'S1',
            'is_active' => 1,
        ]);

        $this->actingAs($superAdmin)
            ->post(route('master-data.students.store'), [
                'study_program_id' => $studyProgram->id,
                'student_number' => self::STUDENT_NUMBER,
                'name' => 'Budi',
                'email' => 'budi@example.com',
                'phone' => '08123456789',
                'address' => 'Bandung',
                'is_active' => 1,
            ])
            ->assertRedirect(route('master-data.students.index'));

        $student = Student::where('student_number', self::STUDENT_NUMBER)->firstOrFail();

        $this->actingAs($superAdmin)
            ->put(route('master-data.students.update', $student), [
                'study_program_id' => $studyProgram->id,
                'student_number' => self::STUDENT_NUMBER,
                'name' => 'Budi Updated',
                'email' => 'budi.updated@example.com',
                'phone' => '08123456780',
                'address' => 'Jakarta',
                'is_active' => 0,
            ])
            ->assertRedirect(route('master-data.students.index'));

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Budi Updated',
            'is_active' => 0,
        ]);

        $this->actingAs($superAdmin)
            ->delete(route('master-data.students.destroy', $student))
            ->assertRedirect(route('master-data.students.index'));

        $this->assertDatabaseMissing('students', [
            'id' => $student->id,
        ]);
    }

    public function test_super_admin_can_crud_class_participants_with_related_program_consistency(): void
    {
        $superAdmin = $this->createSuperAdmin();

        $academicYear = AcademicYear::create([
            'name' => '2025/2026',
            'start_year' => 2025,
            'end_year' => 2026,
            'is_active' => 1,
        ]);

        $studyProgram = StudyProgram::create([
            'code' => self::STUDY_PROGRAM_CODE,
            'name' => self::STUDY_PROGRAM_NAME,
            'degree_level' => 'S1',
            'is_active' => 1,
        ]);

        $class = AcademicClass::create([
            'study_program_id' => $studyProgram->id,
            'name' => 'TI-1A',
            'level' => 1,
            'is_active' => 1,
        ]);

        $course = Course::create([
            'study_program_id' => $studyProgram->id,
            'code' => 'TI101',
            'name' => 'Algoritma',
            'credits' => 3,
            'semester' => 1,
            'is_active' => 1,
        ]);

        $student = Student::create([
            'study_program_id' => $studyProgram->id,
            'student_number' => self::STUDENT_NUMBER,
            'name' => 'Budi',
            'is_active' => 1,
        ]);

        $this->actingAs($superAdmin)
            ->post(route('master-data.class-participants.store'), [
                'academic_year_id' => $academicYear->id,
                'study_program_id' => $studyProgram->id,
                'class_id' => $class->id,
                'course_id' => $course->id,
                'student_id' => $student->id,
                'participant_status' => 1,
                'notes' => 'Peserta aktif',
            ])
            ->assertRedirect(route('master-data.class-participants.index'));

        $participant = ClassParticipant::firstOrFail();

        $this->actingAs($superAdmin)
            ->put(route('master-data.class-participants.update', $participant), [
                'academic_year_id' => $academicYear->id,
                'study_program_id' => $studyProgram->id,
                'class_id' => $class->id,
                'course_id' => $course->id,
                'student_id' => $student->id,
                'participant_status' => 2,
                'notes' => 'Keluar',
            ])
            ->assertRedirect(route('master-data.class-participants.index'));

        $this->assertDatabaseHas('class_participants', [
            'id' => $participant->id,
            'participant_status' => 2,
        ]);

        $this->actingAs($superAdmin)
            ->delete(route('master-data.class-participants.destroy', $participant))
            ->assertRedirect(route('master-data.class-participants.index'));

        $this->assertDatabaseMissing('class_participants', [
            'id' => $participant->id,
        ]);
    }

    private function createSuperAdmin(): User
    {
        $role = Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Administrator',
        ]);

        $user = User::factory()->create();
        $user->roles()->sync([$role->id]);

        return $user;
    }
}
