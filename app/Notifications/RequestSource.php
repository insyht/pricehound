<?php

namespace App\Notifications;

use App\Dto\GetSource;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RequestSource extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected GetSource $command)
    {
    }

    public function via(object $notifiable): array
    {
        // Only worth queueing a push when the user actually has an app install to push to.
        if ($notifiable->devices()->exists()) {
            $channels[] = FcmChannel::class;
        }

        return $channels;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->command->id,
            'url' => $this->command->url,
            'headers' => json_encode($this->command->headers),
        ];
    }

    public function toFcm(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
