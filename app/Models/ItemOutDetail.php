<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemOutDetail extends Model
{
    // define fillable fields
    protected $fillable = [
        'item_out_id',
        'product_id',
        'quantity',
        'note',
    ];

    // cast fields
    protected $casts = [
        'quantity' => 'float',
    ];

    // define default values for attributes
    protected $attributes = [
        'note' => '-',
    ];

    // Relationship with ItemOut model
    public function itemOut()
    {
        return $this->belongsTo(ItemOut::class);
    }

    // Relationship with Product model
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
