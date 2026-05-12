<?php

namespace Database\Seeders;

use App\Models\AcademicClass;
use App\Models\Course;
use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class AcademicMasterSeeder extends Seeder
{
    public function run(): void
    {
        $si = StudyProgram::firstOrCreate(
            ['code' => 'SI'],
            ['name' => 'Sistem Informasi', 'degree_level' => 'S1', 'is_active' => true]
        );

        $ti = StudyProgram::firstOrCreate(
            ['code' => 'TI'],
            ['name' => 'Teknik Informatika', 'degree_level' => 'S1', 'is_active' => true]
        );

        AcademicClass::firstOrCreate([
            'study_program_id' => $si->id,
            'name' => 'SI-1A',
        ], [
            'level' => 1,
            'is_active' => true,
        ]);

        AcademicClass::firstOrCreate([
            'study_program_id' => $ti->id,
            'name' => 'TI-1A',
        ], [
            'level' => 1,
            'is_active' => true,
        ]);

        Course::firstOrCreate(['code' => 'SI101'], [
            'study_program_id' => $si->id,
            'name' => 'Dasar Sistem Informasi',
            'credits' => 3,
            'semester' => 1,
            'is_active' => true,
        ]);

        Course::firstOrCreate(['code' => 'TI101'], [
            'study_program_id' => $ti->id,
            'name' => 'Algoritma dan Pemrograman',
            'credits' => 3,
            'semester' => 1,
            'is_active' => true,
        ]);
    }
}
