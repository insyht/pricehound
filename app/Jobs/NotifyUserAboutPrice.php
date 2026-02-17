<?php

namespace App\Jobs;

use App\Models\Price;
use App\Models\User;
use App\Notifications\PriceChange;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyUserAboutPrice implements ShouldQueue
{
    use Queueable;

    public function __construct(protected User $user, protected Price $price)
    {
    }

    public function handle(): void
    {
        $lastPriceBeforeThisOne = Price::withoutGlobalScope('mine')
                                       ->where('product_id', $this->price->product_id)
                                       ->where('user_id', $this->user->id)
                                       ->where('fetched_at', '>', $this->price->fetched_at)
                                       ->orderByDesc('fetched_at')
                                       ->first();
        if (!$lastPriceBeforeThisOne || $lastPriceBeforeThisOne->price > $this->price->price) {
            // The new price is either lower than the previous one or this is the first known price -> notify user
            // todo Use special rules to determine whether to notify user (for instance: only when below specific price)
            $this->user->notify(new PriceChange($this->price, $this->user));
        }

    }
}
