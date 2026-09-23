<?php

namespace App\Jobs;

use App\Dto\GetSource;
use App\Models\Request;
use App\Notifications\RequestSource;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DispatchGetSourceToPack implements ShouldQueue
{
    use Queueable;

    public function __construct(protected GetSource $command)
    {
    }

    public function handle(): void
    {
        if (stripos($this->command->callbackUrl, $this->command->hound->url) !== 0) {
            // The callback url should start with the same url as the hound.
            // This check is to prevent a malicious "Hound" from forcing us to send requests to urls they don't own
            return;
        }

        // todo I need to perform a check/validation on $this->command->url to prevent our users from becoming part of a botnet for a malicious "Hound"

        Request::create(
            [
                'request_id' => $this->command->id,
                'hound_id' => $this->command->hound->id,
                'url' => $this->command->url,
                'headers' => $this->command->headers,
                'callback_url' => $this->command->callbackUrl,
            ]
        );

        foreach ($this->command->hound->users as $user) {
            $user->notify(new RequestSource($this->command));
        }
    }
}
