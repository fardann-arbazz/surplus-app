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
        Schema::create('surplus_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->integer('initial_price');
            $table->integer('discount_price');
            $table->integer('quantity');
            $table->integer('remaining_quantity');
            $table->dateTime('expired_at');
            $table->dateTime('pickup_start_at');
            $table->dateTime('pickup_end_at');
            $table->enum('status', ['active', 'sold_out', 'expired']);

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['status', 'expired_at']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surplus_products');
    }
};
