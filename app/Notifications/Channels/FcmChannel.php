<?php

namespace App\Notifications\Channels;

use App\Services\FcmSender;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;

/**
 * Delivers a notification to every device the user has registered.
 *
 * Notifications opt in by declaring a toFcm() method returning at least `title` and `body`;
 * any other key is passed through as FCM data (the app reads `product_id` to deep-link).
 */
class FcmChannel
{
    public function __construct(private readonly FcmSender $sender) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $message = $notification->toFcm($notifiable);

        foreach ($notifiable->devices as $device) {
            $this->sender->send(
                $device,
                $message['title'] ?? '',
                $message['body'] ?? '',
                Arr::except($message, ['title', 'body'])
            );
        }
    }
}
