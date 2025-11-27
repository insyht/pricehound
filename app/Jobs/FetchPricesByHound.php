<?php

namespace App\Jobs;

use App\Models\Hound;
use App\Models\ProductShop;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchPricesByHound implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Hound $hound)
    {
    }

    public function handle(): void
    {
        if (!$this->hound->online) {
            return;
        }

        foreach (ProductShop::all() as $item) {
            FetchPrice::dispatch($this->hound, $item);
        }
    }
}
