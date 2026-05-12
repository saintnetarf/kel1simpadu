<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['auditable_type','auditable_id','user_id','old_values','new_values','action'];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];
}
