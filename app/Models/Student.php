<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\StudyProgram;
use App\Models\ClassParticipant;

class Student extends Model
{
    protected $fillable = [
        'study_program_id',
        'student_number',
        'name',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'study_program_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function classParticipants(): HasMany
    {
        return $this->hasMany(ClassParticipant::class);
    }
}

