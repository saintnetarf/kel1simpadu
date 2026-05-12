<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\StudyProgram;
use App\Models\ClassParticipant;

class AcademicClass extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'study_program_id',
        'name',
        'level',
        'description',
        'is_active',
    ];

    protected $casts = [
        'study_program_id' => 'integer',
        'level' => 'integer',
        'is_active' => 'boolean',
    ];

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function classParticipants(): HasMany
    {
        return $this->hasMany(ClassParticipant::class, 'class_id');
    }
}

