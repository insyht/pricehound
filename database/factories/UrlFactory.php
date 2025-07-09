<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Shop;
use App\Models\Url;
use Illuminate\Database\Eloquent\Factories\Factory;

class UrlFactory extends Factory
{
    protected $model = Url::class;

    public function definition(): array
    {
        return [
            'url' => fake()->url(),
            'product_id' => Product::factory(),
            'shop_id' => Shop::factory(),
        ];
    }
}
