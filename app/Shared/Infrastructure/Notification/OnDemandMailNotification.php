<?php

namespace App\Shared\Infrastructure\Notification;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OnDemandMailNotification extends Notification
{
    public function __construct(
        private readonly string $subject,
        private readonly string $body
    ) {
    }

    /**
     * @return string[]
     */
    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return new MailMessage()
            ->subject($this->subject)
            ->line($this->body);
    }
}