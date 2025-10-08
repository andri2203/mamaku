<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemInDetail extends Model
{
    // Define Fillable Attributes
    protected $fillable = [
        'item_in_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    // cast fields
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'integer',
        'subtotal' => 'integer',
    ];

    // Define relationship with Product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship with ItemIn
    public function itemIn(): BelongsTo
    {
        return $this->belongsTo(ItemIn::class);
    }
}
