<?php

use App\Enums\PriceRuleTypes;
use App\Jobs\NotifyUserAboutPrice;
use App\Models\Price;
use App\Models\PriceRule;
use App\Models\Product;
use App\Models\ProductUser;
use App\Models\User;
use App\Notifications\PriceChange;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Money\Currency;
use Money\Money;

pest()->use(RefreshDatabase::class);

function belowPriceRule(string $value): PriceRule
{
    $productUser = ProductUser::factory()->create();

    $rule = new PriceRule();
    $rule->product_user_id = $productUser->id;
    $rule->type = PriceRuleTypes::BELOW_PRICE->value;
    $rule->value = $value;
    $rule->save();

    return $rule;
}

function priceOf(int $amount, Product $product, User $user, string $fetchedAt): Price
{
    return Price::factory()->create([
        'price' => new Money($amount, new Currency('EUR')),
        'currency' => 'EUR',
        'product_id' => $product->id,
        'user_id' => $user->id,
        'fetched_at' => $fetchedAt,
    ]);
}

it('notifies on the very first price without a previous price to compare against', function () {
    Notification::fake();

    // "below_price 10" is satisfied by a single fetch: there is nothing to compare against, so
    // the notification is built with no old price at all.
    $rule = belowPriceRule('10');
    $onlyPrice = priceOf(9, $rule->product, $rule->user, '2026-07-19 10:00:00');

    (new NotifyUserAboutPrice($onlyPrice))->handle(app(App\Helpers\NotificationHelper::class));

    Notification::assertSentTo($rule->user, PriceChange::class);
});

it('renders mail and push for a first price without crashing', function () {
    $rule = belowPriceRule('10');
    $onlyPrice = priceOf(9, $rule->product, $rule->user, '2026-07-19 10:00:00');

    $notification = new PriceChange(null, $onlyPrice);

    expect($notification->toFcm($rule->user))
        ->toHaveKey('product_id', $onlyPrice->product_id)
        ->and($notification->toMail($rule->user)->render())->not->toBeEmpty();
});

it('still compares against the previous price when there is one', function () {
    $rule = belowPriceRule('10');
    $oldPrice = priceOf(12, $rule->product, $rule->user, '2026-07-19 09:00:00');
    $newPrice = priceOf(9, $rule->product, $rule->user, '2026-07-19 10:00:00');

    $body = (new PriceChange($oldPrice, $newPrice))->toFcm($rule->user)['body'];

    // 9 vs 12 is a 25% drop; the push body should say so.
    expect($body)->toContain('0.12')->toContain('-25.0%');
});
