<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Application\Service\EmailNotifierInterface;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Infrastructure\Notification\OnDemandEmailNotifiable;
use App\Shared\Infrastructure\Notification\OnDemandMailNotification;
use Illuminate\Contracts\Notifications\Dispatcher;

readonly class NotificationEmailNotifier implements EmailNotifierInterface
{
    public function __construct(
        private Dispatcher $dispatcher
    ) {
    }

    public function notify(Email $email, string $subject, string $body): void
    {
        $this->dispatcher->send(
            new OnDemandEmailNotifiable($email->value),
            new OnDemandMailNotification($subject, $body)
        );
    }
}