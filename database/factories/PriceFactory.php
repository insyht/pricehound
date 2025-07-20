<?php

namespace Database\Factories;

use App\Models\Price;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceFactory extends Factory
{
    protected $model = Price::class;

    public function definition(): array
    {
        return [
            'url' => fake()->url(),
            'product_id' => Product::factory(),
            'shop_id' => Shop::factory(),
            'xpath_price' => $this->generateRandomXpath(),
            'price' => fake()->numberBetween(1, 10000000), // between one cent and 100.000 euros
            'currency' => fake()->currencyCode(),
        ];
    }

    // todo Duplicate code with ShopFactory, move to it's own class
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
    }}
