<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemOut extends Model
{
    // define fillable fields
    protected $fillable = [
        'user_id',
        'total_item',
        'note',
        'status',
    ];

    // cast fields
    protected $casts = [
        'total_item' => 'integer',
        'status' => \App\ItemOutStatusEnum::class,
    ];

    // define default values for attributes
    protected $attributes = [
        'note' => '-',
    ];

    // Relationship with ItemOutDetail model
    public function details()
    {
        return $this->hasMany(ItemOutDetail::class);
    }

    // Relationship with User model
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
