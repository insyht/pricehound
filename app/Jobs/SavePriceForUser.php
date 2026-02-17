<?php

namespace App\Jobs;

use App\Models\Price;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Money\Currency;
use Money\Money;
use Throwable;

class SavePriceForUser implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $uniqueFor = 3600;

    public function __construct(
        protected User $user,
        protected Product $product,
        protected string $currency,
        protected string $url,
        protected Carbon $createdAt,
        protected int $price
    ) {}

    public function handle(): void
    {
        try {
            $priceValue = new Money($this->price, new Currency($this->currency));

            $latestPrice = Price::withoutGlobalScope('mine')->where('product_id', $this->product->id)
                 ->where('user_id', $this->user->id)
                 ->orderByDesc('fetched_at')
                ->first();
            if (!$latestPrice || !$latestPrice->price->equals($priceValue)) {
                $price = Price::create(
                    [
                        'price' => $priceValue,
                        'currency' => $this->currency,
                        'url' => $this->url,
                        'user_id' => $this->user->id,
                        'hound_id' => $this->user->hound->id,
                        'product_id' => $this->product->id,
                        'fetched_at' => Carbon::now(),
                        'created_at' => $this->createdAt,
                    ]
                );
                NotifyUserAboutPrice::dispatch($this->user, $price);
            }
        } catch (Throwable $t) {
            Log::warning(
                'Could not save price',
                ['data' => $this, 'error' => $t->getMessage()]
            );
        }
    }

    public function uniqueId(): string
    {
        return sprintf(
            '%s_%s_%d',
            $this->user->id,
            $this->product->id,
            $this->price
        );
    }
}
