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
        Schema::create('item_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->integer('total_item');
            $table->string('note')->default('-');
            $table->enum('status', ['rusak', 'kadaluarsa', 'hilang']);
            $table->timestamps();
        });

        Schema::create('item_out_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_out_id')->references('id')->on('item_outs');
            $table->foreignId('product_id')->references('id')->on('products');
            $table->float('quantity');
            $table->string('note')->default('-');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_out_details');
        Schema::dropIfExists('item_outs');
    }
};
