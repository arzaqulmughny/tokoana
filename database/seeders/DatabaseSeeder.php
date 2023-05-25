<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([
            'name' => "Bos Dermawan",
            'username' => "admin",
            'password' => bcrypt('admin123'),
            'user_level' => '1',
        ]);

        Supplier::factory(10)->create();

        $units = ['kg', 'L', 'pcs', 'g', 'ons', 'cm'];
        foreach ($units as $unit) {
            ProductUnit::create([
                'name' => $unit
            ]);
        };

        $categories = ['drink', 'food', 'tool', 'stationery', 'grocery'];
        foreach ($categories as $category) {
            ProductCategory::create([
                'name' => $category
            ]);
        };
    }
}
