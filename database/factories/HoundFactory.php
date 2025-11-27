<?php

namespace Database\Factories;

use App\Models\Hound;
use Illuminate\Database\Eloquent\Factories\Factory;

class HoundFactory extends Factory
{
    protected $model = Hound::class;

    public function definition(): array
    {
        return [
            'title' => fake()->word(),
            'description' => fake()->text(),
            'url' => fake()->url(),
            'last_ping' => fake()->dateTimeBetween('-3 days'),
            'online' => fake()->boolean(90),
        ];
    }
}
