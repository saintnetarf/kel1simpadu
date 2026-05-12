<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRegistry extends Model
{
    protected $table = 'service_registry';

    protected $fillable = [
        'name',
        'code',
        'base_url',
        'health_check_url',
        'contact_email',
        'status',
        'description',
    ];
}
