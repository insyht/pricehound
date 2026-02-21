<?php

namespace Database\Seeders;

use App\Enums\PriceRuleTypes;
use App\Jobs\FetchPricesForUser;
use App\Models\Hound;
use App\Models\Price;
use App\Models\PriceRule;
use App\Models\Product;
use App\Models\ProductUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Money\Currency;
use Money\Money;

class Test extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
                'url' => 'https://pricehound-api.ddev.site/api',
                'online' => true,
                'last_ping' => Carbon::now(),
            ]
        );

        $jordy = User::create(
            [
                'name' => 'Jordy',
                'email' => 'jordythijs@gmail.com',
                'password' => '$2y$12$tO9JazUmOb6B8ooFYnliLe4uMuwBcKNfImfC.9KqcHKHXmrC6y2WK',
                'fetch_interval' => 5,
                'hound_api_key' => '2|wXMMrddnhGMkiAiwkJadPlufatPHdNMozwUxLwKE89f22252',
            ]
        );
        $otherUser = User::create(
            ['name' => 'X', 'email' => 'x@x.com', 'password' => '$2y$12$tO9JazUmOb6B8ooFYnliLe4uMuwBcKNfImfC.9KqcHKHXmrC6y2WK', 'fetch_interval' => 5]
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
        $thirdProduct = Product::create(
            [
                'title' => 'Third product',
                'ean' => '12345',
                'created_by_user_id' => null,
            ]
        );

        $jordy->products()->save($product);
        $jordy->hound()->associate($defaultHound)->save();

        Price::create(
            [
                'price' => new Money(8999, new Currency('EUR')),
                'currency' => 'EUR',
                'user_id' => $jordy->id,
                'hound_id' => $defaultHound->id,
                'product_id' => $product->id,
                'url' => 'https://www.bol.com/philips-hue-bridge-pro',
                'fetched_at' => fake()->dateTime(),
            ]
        );
        Price::create(
            [
                'price' => new Money(9500, new Currency('EUR')),
                'currency' => 'EUR',
                'user_id' => $jordy->id,
                'hound_id' => $defaultHound->id,
                'product_id' => $product->id,
                'url' => 'https://www.azerty.nl/philips-hue-bridge-pro',
                'fetched_at' => fake()->dateTime(),
            ]
        );
        Price::create(
            [
                'price' => new Money(7700, new Currency('EUR')),
                'currency' => 'EUR',
                'user_id' => $jordy->id,
                'hound_id' => $otherHound->id,
                'product_id' => $product->id,
                'url' => 'https://www.bol.com/philips-hue-bridge-pro',
                'fetched_at' => fake()->dateTime(),
            ]
        );
        Price::create(
            [
                'price' => new Money(6700, new Currency('EUR')),
                'currency' => 'EUR',
                'user_id' => $otherUser->id,
                'hound_id' => $defaultHound->id,
                'product_id' => $anotherProduct->id,
                'url' => 'https://www.bol.com/another-product',
                'fetched_at' => fake()->dateTime(),
            ]
        );
        Price::create(
            [
                'price' => new Money(5900, new Currency('EUR')),
                'currency' => 'EUR',
                'user_id' => $jordy->id,
                'hound_id' => $defaultHound->id,
                'product_id' => $thirdProduct->id,
                'url' => 'https://www.bol.com/third-product',
                'fetched_at' => fake()->dateTime(),
            ]
        );

        PriceRule::create(
            [
                'product_user_id' => ProductUser::where('user_id', $jordy->id)->where('product_id', $product->id)->first()->id,
                'type' => PriceRuleTypes::BELOW_PRICE->value,
                'value' => '8000',
            ]
        );

        FetchPricesForUser::dispatch($jordy);
    }
}
