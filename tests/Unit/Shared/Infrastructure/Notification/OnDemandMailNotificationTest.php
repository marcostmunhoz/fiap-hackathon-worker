<?php

namespace Tests\Unit\Shared\Infrastructure\Notification;

use App\Shared\Infrastructure\Notification\OnDemandMailNotification;

test('via returns mail', function () {
    // Given
    $sut = new OnDemandMailNotification('Subject', 'Body');

    // When
    $result = $sut->via();

    // Then
    expect($result)->toBe(['mail']);
});

test('toMail returns a MailMessage with the subject and body', function () {
    // Given
    $subject = 'Subject';
    $body = 'Body';
    $sut = new OnDemandMailNotification($subject, $body);

    // When
    $message = $sut->toMail();

    // Then
    expect($message->subject)->toBe($subject)
        ->and($message->introLines)->toBe([$body]);
});