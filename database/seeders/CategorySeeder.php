<?php

namespace Database\Seeders;

use App\Models\CategoryProducts;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CategoryProducts::create(
            ['name' => 'Minuman'],
            ['name' => 'Makanan']
        );
    }
}
