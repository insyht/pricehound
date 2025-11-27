<?php

namespace App\Jobs;

use App\Enums\HoundEndpoints;
use App\Models\Hound;
use App\Models\Price;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Money\Currency;
use Money\Money;
use Throwable;

class FetchPrice implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(protected Hound $hound, protected Product $product)
    {
    }

    public function handle(): void
    {
        if (!$this->hound->online) {
            return;
        }

        try {
            $url = rtrim($this->hound->url, '/') . '/';
            $url .= sprintf(
                HoundEndpoints::FetchPriceDebug->value, //HoundEndpoints::FetchPrice->value, // todo Use the correct one after testing
                $this->product->ean,
                Price::where('hound_id', $this->hound->id)
                     ->where('product_id', $this->product->id)
                     ->latest()
                     ->first()->created_at->timestamp
            );
            $response = Http::get($url);

            if ($response->failed()) {
                Log::warning(
                    'Could not fetch price from hound (response failed), dispatching PingHound to check availability',
                    ['hound' => $this->hound->id, 'product' => $this->product->id]
                );
                PingHound::dispatchSync($this->hound);

                return;
            } elseif ($response->successful() && $response->json('price') !== null && $response->json('currency') !== null && $response->json('url') !== null && $response->json('created_at') !== null) {
                Price::create(
                    [
                        'price' => new Money((int) $response->json('price'), new Currency($response->json('currency'))),
                        'currency' => $response->json('currency'),
                        'url' => $response->json('url'),
                        'hound_id' => $this->hound->id,
                        'product_id' => $this->product->id,
                        'created_at' => $response->json('created_at'), // todo Of misschien een extra kolom aanmaken in prices waarin ik opsla wanneer de hound deze prijs opgezocht heeft?
                        'updated_at' => Carbon::now(),
                    ]
                );
            } else {
                Log::warning(
                    'Could not fetch price from hound (invalid response)',
                    ['hound' => $this->hound->id, 'product' => $this->product->id, 'response' => $response->body() ?? '']
                );

                return;

            }
        } catch (Throwable $t) {
            Log::warning(
                'Could not fetch price from hound, dispatching PingHound to check availability',
                ['hound' => $this->hound->id, 'product' => $this->product->id, 'error' => $t->getMessage()]
            );
            PingHound::dispatchSync($this->hound);

            return;
        }

    }

    public function uniqueId()
    {
        return $this->hound->id . '_' . $this->product->id;
    }
}
