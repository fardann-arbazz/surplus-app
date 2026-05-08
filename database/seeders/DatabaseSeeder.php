<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ================= ADMIN =================
        User::updateOrCreate(
            ['email' => 'admin@rantangku.com'],
            [
                'name' => 'Admin Rantangku',
                'password' => Hash::make('password123'),
                'phone' => '081234567890',
                'role' => 'admin',
                'latitude' => -6.200000,   // contoh (Jakarta)
                'longitude' => 106.816666,
            ]
        );

        // ================= SELLER =================
        User::updateOrCreate(
            ['email' => 'seller@rantangku.com'],
            [
                'name' => 'Seller Rantangku',
                'password' => Hash::make('password123'),
                'phone' => '081298765432',
                'role' => 'seller',
                'latitude' => -6.914744,   // contoh (Bandung)
                'longitude' => 107.609810,
            ]
        );

        // ================= USER =================
        User::updateOrCreate(
            ['email' => 'user@rantangku.com'],
            [
                'name' => 'User Rantangku',
                'password' => Hash::make('password123'),
                'phone' => '081298765434',
                'role' => 'user',
            ]
        );
    }
}
