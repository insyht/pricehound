<?php

namespace Database\Factories;

use App\Models\Hound;
use App\Models\Price;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceFactory extends Factory
{
    protected $model = Price::class;

    public function definition(): array
    {
        return [
            'price' => fake()->numberBetween(1, 10000000), // between one cent and 100.000 euros
            'currency' => fake()->currencyCode(),
            'hound_id' => Hound::factory(),
            'product_id' => Product::factory(),
        ];
    }
}
