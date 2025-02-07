<?php

namespace Tests\Unit\User\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidValueException;
use App\Shared\Domain\ValueObject\Email;

test('sanitize ensures the input is a email string', function () {
    // Given
    $validInput = fake()->safeEmail();
    $invalidInput = 'some-string';

    // Then
    expect(new Email($validInput))->not->toBeNull()
        ->and(static fn () => new Email($invalidInput))
        ->toThrow(InvalidValueException::class, 'Invalid email address.');
});