<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    // Fillable fields
    protected $fillable = ['category', 'code'];

    // Relationship with Product model
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id'); // Assuming 'category_id' is the foreign key in products table
    }
}
