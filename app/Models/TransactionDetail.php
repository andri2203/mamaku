<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetail extends Model
{
    // Define fillable fields
    protected $fillable = [
        'item_out_id',
        'product_id',
        'quantity',
        'price',
        'discount',
        'subtotal',
    ];

    // cast fields
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'integer',
        'discount' => 'float',
        'subtotal' => 'integer',
    ];

    // Relationship with Transaction model
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relationship with Product model
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
