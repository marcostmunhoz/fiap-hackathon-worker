<?php

namespace App\Shared\Domain\ValueObject;

/**
 * @template-extends AbstractValueObject<string>
 */
readonly class Email extends AbstractValueObject
{
    protected function sanitize(mixed $value): string
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->throwInvalidValueException('Invalid email address.');
        }

        return (string) $value;
    }
}