<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductShop;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductShopFactory extends Factory
{
    protected $model = ProductShop::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'shop_id' => Shop::factory(),
            'xpath_price' => $this->generateRandomXpath(),
            'url' => fake()->url(),
        ];
    }

    // todo Duplicate of the method in ShopFactory
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
