<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\ClassParticipant;

class Pegawai extends Model
{
    protected $table = 'pegawai';

    protected $fillable = [
        'employee_number',
        'name',
        'email',
        'phone',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function classParticipants(): HasMany
    {
        return $this->hasMany(ClassParticipant::class, 'pegawai_id');
    }
}
