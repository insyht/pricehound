<?php

namespace App\Jobs;

use App\Enums\HoundEndpoints;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchPricesForUser implements ShouldQueue
{
    use Queueable;

    public function __construct(protected User $user)
    {
    }

    public function handle(): void
    {
        if (!$this->user->hound->online) {
            Log::info(
                sprintf('Won\'t try to fetch prices from Hound because it\'s offline'),
                ['hound' => $this->user->hound->id, 'user' => $this->user->id]
            );
            // todo Maybe use a fallback Hound like 'Pricehound Official'?
            return;
        }

        try {
            $identifiers = implode(',', array_column($this->user->products->select('identifier')->toArray(), 'identifier'));
            $url = rtrim($this->user->hound->url, '/') . '/';
            $url .= sprintf(
                HoundEndpoints::FetchPricesForProducts->value,
                $identifiers
            );
            $response = Http::withToken($this->user->hound_api_key)->acceptJson()->get($url); // todo Ik moet nog iets maken om die token te verversen denk ik
            $data = json_decode($response->body(), true);

            switch (true) {
                case $response->unauthorized():
                    Log::warning(
                        'Invalid API token',
                        ['hound' => $this->user->hound->id, 'user' => $this->user]
                    );
                    break;
                case $response->failed():
                    Log::warning(
                        'Could not fetch prices from hound (response failed), dispatching PingHound to check availability',
                        ['hound' => $this->user->hound->id, 'products' => $identifiers]
                    );
                    PingHound::dispatchSync($this->user->hound);
                    break;
                case $response->successful() && is_array($data):
                    foreach ($data as $product) {
                        $productModel = Product::where('identifier', $product['identifier'])->first();
                        if ($productModel === null) {
                            Log::warning(
                                'Product not found while fetching prices for user',
                                ['identifier' => $product['identifier'], 'user_id' => $this->user->id, 'hound' => $this->user->hound->id]
                            );
                            continue;
                        }
                        // ->utc() so the stored value is UTC wall-clock: Eloquent formats a Carbon in
                        // its own timezone, so an offset like +02:00 would otherwise be stored verbatim.
                        $checkedAt = $product['checked_at'] ? Carbon::parse($product['checked_at'])->utc() : null;
                        SavePriceForUser::dispatch($this->user, $productModel, $product['currency'], $product['url'], Carbon::parse($product['created_at'])->utc(), $product['price'], $checkedAt);
                    }
                    break;
                default:
                    Log::warning(
                        'Could not fetch prices from hound (invalid response)',
                        ['hound' => $this->user->hound->id, 'products' => $identifiers, 'response' => $response->body() ?? '']
                    );
                    break;
            }
        } catch (Throwable $t) {
            Log::warning(
                'Could not fetch prices from hound, dispatching PingHound to check availability',
                ['hound' => $this->user->hound->id, 'products' => $identifiers, 'error' => $t->getMessage()]
            );
            PingHound::dispatchSync($this->user->hound);
        }

        $this->user->update(['next_fetch' => Carbon::now()->addMinutes($this->user->fetch_interval)]);
    }
}
