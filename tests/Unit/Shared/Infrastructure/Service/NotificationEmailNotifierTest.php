<?php

namespace Tests\Unit\Shared\Infrastructure\Service;

use App\Shared\Domain\ValueObject\Email;
use App\Shared\Infrastructure\Notification\OnDemandEmailNotifiable;
use App\Shared\Infrastructure\Notification\OnDemandMailNotification;
use App\Shared\Infrastructure\Service\NotificationEmailNotifier;
use Illuminate\Contracts\Notifications\Dispatcher;
use Mockery;

beforeEach(function () {
    $this->dispatcherMock = Mockery::mock(Dispatcher::class);
    $this->sut = new NotificationEmailNotifier($this->dispatcherMock);
});

test('notify sends on demand notification for given email', function () {
    // Given
    $email = new Email(fake()->safeEmail());
    $subject = fake()->sentence();
    $body = fake()->sentence();
    $this->dispatcherMock->allows('send');

    // When
    $this->sut->notify($email, $subject, $body);

    // Then
    $this->dispatcherMock
        ->shouldHaveReceived('send')
        ->with(
            Mockery::on(
                static fn (OnDemandEmailNotifiable $notifiable) => $notifiable->email === $email->value
            ),
            Mockery::type(OnDemandMailNotification::class)
        );
});
