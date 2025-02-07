<?php

namespace App\Shared\Infrastructure\Notification;

use Illuminate\Notifications\RoutesNotifications;

readonly class OnDemandEmailNotifiable
{
    use RoutesNotifications;

    public function __construct(
        public string $email
    ) {
    }

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }
}