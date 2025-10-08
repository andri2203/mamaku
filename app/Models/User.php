<?php

namespace App\Models;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'level',
        'current_team_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // define relationship to Team model
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id'); // assuming 'current_team_id' is the foreign key in users table
    }

    // define relaationship to Transaction model
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // define relatioinship to ItemOut model
    public function itemOuts()
    {
        return $this->hasMany(ItemOut::class);
    }
}
