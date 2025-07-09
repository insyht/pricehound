<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shop>
 */
class ShopFactory extends Factory
{
    protected $model = \App\Models\Shop::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'xpath_price' => $this->generateRandomXpath(),
        ];
    }

    private function generateRandomXpath(): string
    {
        $elements = ['div', 'span', 'p', 'ul', 'li', 'section', 'article'];
        $attributes = ['id', 'class', 'data-test'];
        $depth = rand(2, 5);
        $xpath = '/html/body';

        for ($i = 0; $i < $depth; $i++) {
            $element = $elements[array_rand($elements)];
            if (rand(0, 1)) {
                $attr = $attributes[array_rand($attributes)];
                $value = fake()->word();
                $xpath .= "/{$element}[@{$attr}=\"{$value}\"]";
            } else {
                $xpath .= "/{$element}";
            }
        }

        return $xpath;
    }
}
