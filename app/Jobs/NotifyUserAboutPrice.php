<?php

namespace App\Jobs;

use App\Helpers\NotificationHelper;
use App\Models\Price;
use App\Notifications\PriceChange;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class NotifyUserAboutPrice implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Price $price)
    {
    }

    public function handle(NotificationHelper $helper): void
    {
        $lastPriceBeforeThisOne = Price::withoutGlobalScope('mine')
                                       ->where('product_id', $this->price->product_id)
                                       ->where('user_id', $this->price->user_id)
                                       ->where('fetched_at', '<', $this->price->fetched_at)
                                       ->orderByDesc('fetched_at')
                                       ->first();
        if ($helper->shouldSendPriceNotification($lastPriceBeforeThisOne, $this->price) && $this->price->notified === false) {

            $this->price->user->notify(new PriceChange($lastPriceBeforeThisOne, $this->price));
            $this->price->update(['notified' => true]);
        } else {
            Log::debug(
                'No notification sent because the price change did not match any price rules',
                ['old_price' => $lastPriceBeforeThisOne, 'new_price' => $this->price, 'rules' => $this->price->rules()]
            );
        }
    }
}
