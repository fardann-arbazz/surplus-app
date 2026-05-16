<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE surplus_products
            ALTER COLUMN expired_at TYPE TIMESTAMP
            USING expired_at::timestamp
        ");

        DB::statement("
            ALTER TABLE surplus_products
            ALTER COLUMN pickup_start_at TYPE TIME
            USING '00:00:00'::time
        ");

        DB::statement("
            ALTER TABLE surplus_products
            ALTER COLUMN pickup_end_at TYPE TIME
            USING '00:00:00'::time
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE surplus_products
            ALTER COLUMN expired_at TYPE DATE
            USING expired_at::date
        ");

        DB::statement("
            ALTER TABLE surplus_products
            ALTER COLUMN pickup_start_at TYPE DATE
            USING CURRENT_DATE
        ");

        DB::statement("
            ALTER TABLE surplus_products
            ALTER COLUMN pickup_end_at TYPE DATE
            USING CURRENT_DATE
        ");
    }
};
