<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\ProductCategory;
use App\Models\ProductList;
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
        // Create a main account, this account is allowed to do all actions
        // Username and password used for authentication
        User::create([
            'name' => "Admin",
            'username' => "admin",
            'password' => bcrypt('admin1234'),
            'user_level' => '1',
        ]);
    }
}
