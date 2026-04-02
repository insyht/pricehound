<?php

namespace Database\Factories;

use App\Models\Hound;
use App\Models\Price;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Money\Currency;
use Money\Money;

class PriceFactory extends Factory
{
    protected $model = Price::class;

    public function definition(): array
    {
        return [
            'currency' => fake()->currencyCode(),
            'price' => fn (array $attributes) => new Money(fake()->numberBetween(1, 10000000), new Currency($attributes['currency'])),
            'url' => fake()->url(),
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'hound_id' => Hound::factory(),
            'fetched_at' => fake()->dateTime(),
        ];
    }
}
