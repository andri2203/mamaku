<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    // Define fillable attributes
    protected $fillable = ['name', 'contact', 'address'];

    // Define relationship with ItemIn
    public function itemIns(): HasMany
    {
        return $this->hasMany(ItemIn::class);
    }
}
