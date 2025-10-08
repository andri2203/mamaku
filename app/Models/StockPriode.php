<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPriode extends Model
{
    // Fillable fields
    protected $fillable = [
        'month',
        'year',
        'product_id',
        'starting_stock',
        'final_stock',
    ];

    // cast fields
    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'product_id' => 'integer',
        'starting_stock' => 'float',
        'final_stock' => 'float',
    ];

    // define default values for attributes
    protected $attributes = [
        'starting_stock' => 0,
        'final_stock' => 0,
    ];

    // Relationship with Product model
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id'); // Assuming 'product_id' is the foreign key in stock_priodes table
    }
}
