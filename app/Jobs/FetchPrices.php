<?php

namespace App\Jobs;

use App\Models\Hound;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchPrices implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    public function handle(): void
    {
        $hounds = Hound::whereHas('users')->get();
        foreach ($hounds as $hound) {
            FetchPricesByHound::dispatch($hound);
        }
    }
}
