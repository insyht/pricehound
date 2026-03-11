<?php

namespace App\Notifications;

use App\Helpers\NotificationHelper;
use App\Models\Price;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PriceChange extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Price $oldPrice, protected Price $newPrice) {}

    public function via(object $notifiable): array
    {
        // todo Determine through which channels / ways to notify this user instead of simply using email
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $divided = bcdiv($this->newPrice->price->getAmount(), $this->oldPrice->price->getAmount(), 4);
        $percentage = bcmul('100', bcsub($divided, '1', 4), 0);
        $sign = '';
        if ($percentage > 0) {
            $sign = '+';
        }

        return (new MailMessage)
            ->subject(__('pricehound.NotificationPriceChangeSubject', ['productname' => $this->newPrice->product->title]))
            ->greeting(sprintf(__('pricehound.NotificationPriceChangeGreeting'), $this->newPrice->user->name))
            ->line(
                sprintf(
                    __('pricehound.NotificationPriceChangeLine1'),
                    $this->newPrice->product->title,
                    $this->newPrice->currency,
                    $this->newPrice->price->getAmount() / 100
                )
            )
            ->line(
                sprintf(
                    __('pricehound.NotificationPriceChangeLine2'),
                    $this->oldPrice->currency,
                    $this->oldPrice->price->getAmount() / 100,
                    $sign,
                    $percentage
                )
            )
            ->line(
                sprintf(
                    __('pricehound.NotificationPriceChangeLine3'),
                    implode(',', resolve(NotificationHelper::class)->getTriggeredRules($this->oldPrice, $this->newPrice))
                )
            )
            ->action(__('pricehound.NotificationPriceChangeAction'), $this->newPrice->url);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->newPrice->product->id,
            'old_currency' => $this->oldPrice->currency,
            'old_price' => $this->oldPrice->price,
            'new_currency' => $this->newPrice->currency,
            'new_price' => $this->newPrice->price,
        ];
    }
}
