<?php

use App\Enums\PriceRuleTypes;
use App\Helpers\NotificationHelper;
use App\Models\Price;
use App\Models\PriceRule;
use App\Models\Product;
use App\Models\ProductUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Money\Currency;
use Money\Money;

pest()->use(RefreshDatabase::class);

beforeEach(function () {
    $this->notificationHelper = new NotificationHelper();
});

function createPriceRule(PriceRuleTypes $type, string $value): PriceRule
{
    $productUser = ProductUser::factory()->create();

    $rule = new PriceRule();
    $rule->product_user_id = $productUser->id;
    $rule->type = $type->value;
    $rule->value = $value;
    $rule->save();

    return $rule;
}

function createPrice(int $priceValue, Product $product, User $user): Price
{
    $price = Price::factory()->create(
        [
            'price' => new Money($priceValue, new Currency('EUR')),
            'currency' => 'EUR',
            'product_id' => $product->id,
            'user_id' => $user->id,
        ]
    );
    $price->save();

    return $price;
}

it('returns true when the rule is "BELOW_PRICE 10" and the price goes from 12 to 9', function () {
    $rule = createPriceRule(PriceRuleTypes::BELOW_PRICE, 10);

    $oldPrice = createPrice(12, $rule->product, $rule->user);
    $newPrice = createPrice(9, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeTrue();
});

it('returns false when the rule is "BELOW_PRICE 10" and the price goes from 12 to 11', function () {
    $rule = createPriceRule(PriceRuleTypes::BELOW_PRICE, 10);

    $oldPrice = createPrice(12, $rule->product, $rule->user);
    $newPrice = createPrice(11, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeFalse();
});

it('returns false when the rule is "BELOW_PRICE 10" and the price goes from 12 to 10', function () {
    $rule = createPriceRule(PriceRuleTypes::BELOW_PRICE, 10);

    $oldPrice = createPrice(12, $rule->product, $rule->user);
    $newPrice = createPrice(10, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeFalse();
});

it('returns true when the rule is "ABOVE_PRICE 10" and the price goes from 8 to 12', function () {
    $rule = createPriceRule(PriceRuleTypes::ABOVE_PRICE, 10);

    $oldPrice = createPrice(8, $rule->product, $rule->user);
    $newPrice = createPrice(12, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeTrue();
});

it('returns false when the rule is "ABOVE_PRICE 10" and the price goes from 8 to 9', function () {
    $rule = createPriceRule(PriceRuleTypes::ABOVE_PRICE, 10);

    $oldPrice = createPrice(8, $rule->product, $rule->user);
    $newPrice = createPrice(9, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeFalse();
});

it('returns false when the rule is "ABOVE_PRICE 10" and the price goes from 8 to 10', function () {
    $rule = createPriceRule(PriceRuleTypes::ABOVE_PRICE, 10);

    $oldPrice = createPrice(8, $rule->product, $rule->user);
    $newPrice = createPrice(10, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeFalse();
});

it('returns true when the rule is "DECREASE_PERCENTAGE 10" and the price goes from 100 to 80 (-20%)', function () {
    $rule = createPriceRule(PriceRuleTypes::DECREASE_PERCENTAGE, 10);

    $oldPrice = createPrice(100, $rule->product, $rule->user);
    $newPrice = createPrice(80, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeTrue();
});

it('returns true when the rule is "DECREASE_PERCENTAGE 10" and the price goes from 100 to 90 (-10%)', function () {
    $rule = createPriceRule(PriceRuleTypes::DECREASE_PERCENTAGE, 10);

    $oldPrice = createPrice(100, $rule->product, $rule->user);
    $newPrice = createPrice(90, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeTrue();
});

it('returns false when the rule is "DECREASE_PERCENTAGE 10" and the price goes from 100 to 95 (-5%)', function () {
    $rule = createPriceRule(PriceRuleTypes::DECREASE_PERCENTAGE, 10);

    $oldPrice = createPrice(100, $rule->product, $rule->user);
    $newPrice = createPrice(95, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeFalse();
});

it('returns true when the rule is "INCREASE_PERCENTAGE 10" and the price goes from 100 to 120 (+20%)', function () {
    $rule = createPriceRule(PriceRuleTypes::INCREASE_PERCENTAGE, 10);

    $oldPrice = createPrice(100, $rule->product, $rule->user);
    $newPrice = createPrice(120, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeTrue();
});

it('returns true when the rule is "INCREASE_PERCENTAGE 10" and the price goes from 100 to 110 (+10%)', function () {
    $rule = createPriceRule(PriceRuleTypes::INCREASE_PERCENTAGE, 10);

    $oldPrice = createPrice(100, $rule->product, $rule->user);
    $newPrice = createPrice(110, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeTrue();
});

it('returns false when the rule is "INCREASE_PERCENTAGE 10" and the price goes from 100 to 105 (+5%)', function () {
    $rule = createPriceRule(PriceRuleTypes::INCREASE_PERCENTAGE, 10);

    $oldPrice = createPrice(100, $rule->product, $rule->user);
    $newPrice = createPrice(105, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeFalse();
});

it('returns true when the rule is "DECREASE_AMOUNT 10" and the price goes from 100 to 80', function () {
    $rule = createPriceRule(PriceRuleTypes::DECREASE_AMOUNT, 10);

    $oldPrice = createPrice(100, $rule->product, $rule->user);
    $newPrice = createPrice(80, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeTrue();
});

it('returns true when the rule is "DECREASE_AMOUNT 10" and the price goes from 100 to 90', function () {
    $rule = createPriceRule(PriceRuleTypes::DECREASE_AMOUNT, 10);

    $oldPrice = createPrice(100, $rule->product, $rule->user);
    $newPrice = createPrice(90, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeTrue();
});

it('returns false when the rule is "DECREASE_AMOUNT 10" and the price goes from 100 to 95', function () {
    $rule = createPriceRule(PriceRuleTypes::DECREASE_AMOUNT, 10);

    $oldPrice = createPrice(100, $rule->product, $rule->user);
    $newPrice = createPrice(95, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeFalse();
});

it('returns true when the rule is "INCREASE_AMOUNT 10" and the price goes from 100 to 120', function () {
    $rule = createPriceRule(PriceRuleTypes::INCREASE_AMOUNT, 10);

    $oldPrice = createPrice(100, $rule->product, $rule->user);
    $newPrice = createPrice(120, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeTrue();
});

it('returns true when the rule is "INCREASE_AMOUNT 10" and the price goes from 100 to 110', function () {
    $rule = createPriceRule(PriceRuleTypes::INCREASE_AMOUNT, 10);

    $oldPrice = createPrice(100, $rule->product, $rule->user);
    $newPrice = createPrice(110, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeTrue();
});

it('returns false when the rule is "INCREASE_AMOUNT 10" and the price goes from 100 to 105', function () {
    $rule = createPriceRule(PriceRuleTypes::INCREASE_AMOUNT, 10);

    $oldPrice = createPrice(100, $rule->product, $rule->user);
    $newPrice = createPrice(105, $rule->product, $rule->user);

    expect($this->notificationHelper->shouldSendPriceNotification($oldPrice, $newPrice))->toBeFalse();
});

// todo I should also test for a mix of multiple rules on a product
