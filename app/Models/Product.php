<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    // Fillable fields
    protected $fillable = [
        'category_id',
        'name',
        'code',
        'brand',
        'quantity',
        'price_buy',
        'price_sell',
        'discount',
    ];

    // cast fields
    protected $casts = [
        'quantity' => 'float',
        'price_buy' => 'float',
        'price_sell' => 'float',
        'discount' => 'float',
    ];

    // define default values for attributes
    protected $attributes = [
        'quantity' => 0,
        'discount' => 0,
    ];

    // Relationship with ItemInDetail model
    public function itemInDetails(): HasMany
    {
        return $this->hasMany(ItemInDetail::class);
    }

    // Relationship with ItemInDetail model
    public function itemOutDetails(): HasMany
    {
        return $this->hasMany(ItemOutDetail::class);
    }

    // Relationship with TransactionDetail model
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // Relationship with ProductCategory model
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class); // Assuming 'category_id' is the foreign key in products table
    }

    // Relationship with StockPriode model
    public function stockPriodes(): HasMany
    {
        return $this->hasMany(StockPriode::class);
    }

    // get all brand distinct
    public static function getAllBrands()
    {
        return self::select('brand')->distinct()->pluck('brand');
    }
}
