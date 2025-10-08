<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemIn extends Model
{
    // Define Fillable Attributes
    protected $fillable = [
        'supplier_id',
        'total_item',
        'total_price',
        'is_paid',
    ];

    // cast fields
    protected $casts = [
        'total_item' => 'integer',
        'total_price' => 'integer',
        'is_paid' => 'boolean',
    ];

    // Define relationship with Supplier
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relationship with ItemInDetail
    public function details(): HasMany
    {
        return $this->hasMany(ItemInDetail::class);
    }
}
