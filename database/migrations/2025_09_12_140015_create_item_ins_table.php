<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->references('id')->on('suppliers');
            $table->integer('total_item');
            $table->integer('total_price');
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
        });

        Schema::create('item_in_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_in_id')->references('id')->on('item_ins');
            $table->foreignId('product_id')->references('id')->on('products');
            $table->integer('quantity');
            $table->integer('price');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_in_details');
        Schema::dropIfExists('item_ins');
    }
};
