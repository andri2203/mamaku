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
        Schema::create('stock_priodes', function (Blueprint $table) {
            $table->id();
            $table->integer('month');
            $table->integer('year');
            $table->foreignId('product_id')->references('id')->on('products');
            $table->float('starting_stock')->default(0);
            $table->float('final_stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_priodes');
    }
};
