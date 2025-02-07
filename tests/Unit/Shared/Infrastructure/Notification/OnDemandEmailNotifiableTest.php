<?php

namespace Tests\Unit\Shared\Infrastructure\Notification;

use App\Shared\Infrastructure\Notification\OnDemandEmailNotifiable;

test('routeNotificationForMail returns the email', function () {
    // Given
    $email = fake()->safeEmail();
    $sut = new OnDemandEmailNotifiable($email);

    // When
    $result = $sut->routeNotificationForMail();

    // Then
    expect($result)->toBe($email);
});

