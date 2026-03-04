<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductUserFactory extends Factory
{
    protected $model = ProductUser::class;

    public function definition(): array
    {
        $product = Product::factory()->create();
        $user = User::factory()->create();

        return [
            'product_id' => $product->id,
            'user_id' => $user->id,
        ];
    }
}
