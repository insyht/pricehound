<?php

namespace Database\Seeders;

use App\Jobs\FetchPrices;
use App\Models\Hound;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductShop;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Money\Currency;
use Money\Money;

class Test extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jordy = User::create(
            ['name' => 'Jordy', 'email' => 'jordythijs@gmail.com', 'password' => '$2y$12$tO9JazUmOb6B8ooFYnliLe4uMuwBcKNfImfC.9KqcHKHXmrC6y2WK']
        );
        $otherUser = User::create(
            ['name' => 'X', 'email' => 'x@x.com', 'password' => '$2y$12$tO9JazUmOb6B8ooFYnliLe4uMuwBcKNfImfC.9KqcHKHXmrC6y2WK']
        );

        $bol = Shop::create(
            [
                'name' => 'Bol.com',
                'xpath_price' => '//*[@id="buy-block"]/wsp-visibility-switch/section/section/div[1]/div/span[2]',
            ]
        );
        $azerty = Shop::create(
            [
                'name' => 'Azerty',
                'xpath_price' => '//*[@id="product-price-869834"]/span',
            ]
        );

        $product = Product::create(
            [
                'title' => 'Philips Hue Bridge Pro',
                'ean' => '8720169155114',
                'created_by_user_id' => $jordy->id,
            ]
        );
        $anotherProduct = Product::create(
            [
                'title' => 'Another product',
                'ean' => '4711081440178',
                'created_by_user_id' => $otherUser->id,
            ]
        );
        Product::create(
            [
                'title' => 'Third product',
                'ean' => '12345',
                'created_by_user_id' => null,
            ]
        );

        $jordy->products()->save($product);

        $otherHound = Hound::create(
            [
                'name' => 'Pricehound test',
                'description' => 'TestHound',
                'url' => 'https://iwsklantenz.nl/pricehound',
                'online' => false,
                'last_ping' => null,
            ]
        );
        $defaultHound = Hound::create(
            [
                'name' => 'Pricehound default',
                'description' => 'Official Hound',
                'url' => 'https://iwsklanten.nl/pricehound',
                'online' => true,
                'last_ping' => Carbon::now(),
            ]
        );
        $jordy->hound()->associate($defaultHound)->save();

        $anotherProductShop = ProductShop::create(
            [
                'product_id' => $anotherProduct->id,
                'shop_id' => $bol->id,
                'xpath_price' => 'blabla',
                'url' => 'https://bol.com/anotherproductlink'
        ]
        );
        $thirdProductShop = ProductShop::create(
            [
                'product_id' => $product->id,
                'shop_id' => $azerty->id,
                'xpath_price' => 'blabla',
                'url' => 'https://azerty.nl/hue-bridge-pro'
            ]
        );
        $productShop = ProductShop::create(
            [
                'product_id' => $product->id,
                'shop_id' => $bol->id,
                'xpath_price' => 'blabla',
                'url' => 'https://bol.com/hue-bridge-pro'
          ]
        );

        Price::create(
            [
                'price' => new Money(8999, new Currency('EUR')),
                'currency' => 'EUR',
                'hound_id' => $defaultHound->id,
                'product_shop_id' => $productShop->id,
            ]
        );
        Price::create(
            [
                'price' => new Money(9500, new Currency('EUR')),
                'currency' => 'EUR',
                'hound_id' => $defaultHound->id,
                'product_shop_id' => $productShop->id,
            ]
        );
        Price::create(
            [
                'price' => new Money(7700, new Currency('EUR')),
                'currency' => 'EUR',
                'hound_id' => $otherHound->id,
                'product_shop_id' => $productShop->id,
            ]
        );
        Price::create(
            [
                'price' => new Money(6700, new Currency('EUR')),
                'currency' => 'EUR',
                'hound_id' => $defaultHound->id,
                'product_shop_id' => $anotherProductShop->id,
            ]
        );
        Price::create(
            [
                'price' => new Money(5900, new Currency('EUR')),
                'currency' => 'EUR',
                'hound_id' => $defaultHound->id,
                'product_shop_id' => $thirdProductShop->id,
            ]
        );

        FetchPrices::dispatch();
    }
}
