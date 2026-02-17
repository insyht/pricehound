<?php

namespace App\Notifications;

use App\Models\Price;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PriceChange extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Price $price, protected User $user)
    {
    }

    public function via(object $notifiable): array
    {
        // todo Determine through which channels / ways to notify this user instead of simply using email
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // todo Replace hardcoded texts with translations
        return (new MailMessage)
            ->subject('Prijs van een product is gewijzigd')
            ->greeting(sprintf('Hey %s,', $this->user->name))
            ->line(
                sprintf(
                    '%s is nu te koop voor %s %s.',
                    $this->price->product->title,
                    $this->price->currency,
                    $this->price->price->getAmount() / 100
                )
            )
            ->action('Bekijk de prijs in de shop', $this->price->url);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->price->product->id,
            'currency' => $this->price->currency,
            'price' => $this->price->price,
        ];
    }
}
