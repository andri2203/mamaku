<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    // Define fillable fields
    protected $fillable = [
        'name',
        'address',
        'phone',
    ];

    // cast fields
    protected $casts = [
        'name' => 'string',
        'address' => 'string',
        'phone' => 'string',
    ];

    // Relationship with ItemOut model
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
