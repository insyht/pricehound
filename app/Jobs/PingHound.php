<?php

namespace App\Jobs;

use App\Enums\HoundEndpoints;
use App\Models\Hound;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PingHound implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(protected Hound $hound)
    {
    }

    public function handle(): void
    {
        try {
            $response = Http::get(rtrim($this->hound->url, '/') . '/' . HoundEndpoints::Info->value);

            if ($response->failed()) {
                $this->hound->update(['online' => false]);
            } elseif ($response->successful()) {
                $this->hound->update(
                    [
                        'online' => true,
                        'last_ping' => now(),
                        'name' => $response->json('name', ''),
                        'description' => $response->json('description', ''),
                    ]
                );
            }
        } catch (Throwable $t) {
            $this->hound->update(['online' => false]);
            Log::error(
                'Failed to ping hound',
                [
                    'houndId' => $this->hound->id,
                    'url' => $this->hound->url,
                    'error' => $t->getMessage(),
                ]
            );
        }
    }

    public function uniqueId()
    {
        return $this->hound->id;
    }
}
