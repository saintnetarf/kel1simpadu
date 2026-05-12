<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\AcademicYear;
use App\Models\StudyProgram;
use App\Models\AcademicClass;
use App\Models\Course;
use App\Models\Student;
use App\Models\Pegawai;

class ClassParticipant extends Model
{
    protected $fillable = [
        'academic_year_id',
        'study_program_id',
        'class_id',
        'course_id',
        'student_id',
        'pegawai_id',
        'participant_status',
        'notes',
    ];

    protected $casts = [
        'academic_year_id' => 'integer',
        'study_program_id' => 'integer',
        'class_id' => 'integer',
        'course_id' => 'integer',
        'student_id' => 'integer',
        'pegawai_id' => 'integer',
        'participant_status' => 'integer',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}

