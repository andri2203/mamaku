<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = ['name'];

    // define relationship to User model
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'current_team_id'); // assuming 'current_team_id' is the foreign key in users table
    }
}
