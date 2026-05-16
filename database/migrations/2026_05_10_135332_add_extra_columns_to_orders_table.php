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
        Schema::table('orders', function (Blueprint $table) {
            // Tambah store_id agar seller query lebih cepat
            $table->unsignedBigInteger('store_id')->nullable()->after('user_id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('set null');

            // Kode pickup unik (6 karakter alphanumeric uppercase)
            $table->string('pickup_code', 8)->nullable()->unique()->after('payment_reference');

            // Timestamp
            $table->timestamp('paid_at')->nullable()->after('pickup_code');
            $table->timestamp('expires_at')->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn(['store_id', 'pickup_code', 'paid_at', 'expires_at']);
        });
    }
};
