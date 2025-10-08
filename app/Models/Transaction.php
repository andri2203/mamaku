<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    // Define fillable fields
    protected $fillable = [
        'member_id',
        'user_id',
        'total_item',
        'total_price',
        'discount',
        'is_paid',
    ];

    // cast fields
    protected $casts = [
        'total_item' => 'integer',
        'total_price' => 'integer',
        'discount' => 'float',
        'is_paid' => 'boolean',
    ];

    // Relationship with TransactionDetail model
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // Relationship with Member model
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    // Relationship with User model
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
