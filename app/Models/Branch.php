<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }
}
