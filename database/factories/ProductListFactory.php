<?php

namespace Database\Factories;

use App\Models\ProductList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductList>
 */
class ProductListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


    public function definition(): array
    {
        return [
            'name' => fake()->word() . random_int(1, 100),
            'unit_id' => random_int(1, 5),
            'category_id' => random_int(1, 5),
            'barcode' => fake()->word() . random_int(1000, 10000),
            'price' => random_int(1000, 100000),
        ];
    }
}
