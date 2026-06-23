<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'description',
        'address',
        'main_contact',
        'secondary_contact',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
