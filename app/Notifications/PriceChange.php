<?php

namespace App\Notifications;

use App\Helpers\NotificationHelper;
use App\Models\Price;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PriceChange extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * [$oldPrice] is null when this is the first price ever fetched for the product. A rule that
     * looks at the price on its own (below_price / above_price) fires on that first fetch, so
     * every comparison against the old price has to be optional.
     */
    public function __construct(protected ?Price $oldPrice, protected Price $newPrice) {}

    public function via(object $notifiable): array
    {
        $channels = ['mail'];
        // Only worth queueing a push when the user actually has an app install to push to.
        if ($notifiable->devices()->exists()) {
            $channels[] = FcmChannel::class;
        }

        return $channels;
    }

    /**
     * Push payload. `product_id` makes the app open that product when the notification is tapped.
     */
    public function toFcm(object $notifiable): array
    {
        if ($this->oldPrice === null) {
            $body = sprintf(
                __('pricehound.NotificationPriceChangePushBodyFirst'),
                $this->newPrice->currency,
                $this->newPrice->price->getAmount() / 100
            );
        } else {
            $percentage = $this->percentageChange();
            $body = sprintf(
                __('pricehound.NotificationPriceChangePushBody'),
                $this->newPrice->currency,
                $this->newPrice->price->getAmount() / 100,
                $this->oldPrice->currency,
                $this->oldPrice->price->getAmount() / 100,
                $percentage > 0 ? '+' : '',
                $percentage
            );
        }

        return [
            'title' => $this->newPrice->product->title,
            'body' => $body,
            'product_id' => $this->newPrice->product->id,
        ];
    }

    /** Signed percentage difference between the old and the new price. Requires an old price. */
    private function percentageChange(): string
    {
        $divided = bcdiv($this->newPrice->price->getAmount(), $this->oldPrice->price->getAmount(), 4);

        return bcmul('100', bcsub($divided, '1', 4), 0);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject(__('pricehound.NotificationPriceChangeSubject', ['productname' => $this->newPrice->product->title]))
            ->greeting(sprintf(__('pricehound.NotificationPriceChangeGreeting'), $this->newPrice->user->name))
            ->line(
                sprintf(
                    __('pricehound.NotificationPriceChangeLine1'),
                    $this->newPrice->product->title,
                    $this->newPrice->currency,
                    $this->newPrice->price->getAmount() / 100
                )
            );

        // Nothing to compare against on the first fetch, so the old-price line is left out.
        if ($this->oldPrice !== null) {
            $percentage = $this->percentageChange();
            $mail->line(
                sprintf(
                    __('pricehound.NotificationPriceChangeLine2'),
                    $this->oldPrice->currency,
                    $this->oldPrice->price->getAmount() / 100,
                    $percentage > 0 ? '+' : '',
                    $percentage
                )
            );
        }

        return $mail
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
            'old_currency' => $this->oldPrice?->currency,
            'old_price' => $this->oldPrice?->price,
            'new_currency' => $this->newPrice->currency,
            'new_price' => $this->newPrice->price,
        ];
    }
}
