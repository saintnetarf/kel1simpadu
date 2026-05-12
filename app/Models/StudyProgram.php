<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\AcademicClass;
use App\Models\Course;
use App\Models\Student;
use App\Models\ClassParticipant;

class StudyProgram extends Model
{
    protected $fillable = [
        'code',
        'name',
        'degree_level',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function classes(): HasMany
    {
        return $this->hasMany(AcademicClass::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function classParticipants(): HasMany
    {
        return $this->hasMany(ClassParticipant::class);
    }
}

