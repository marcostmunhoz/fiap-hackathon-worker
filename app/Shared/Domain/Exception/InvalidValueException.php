<?php

namespace App\Shared\Domain\Exception;

/**
 * @codeCoverageIgnore
 */
class InvalidValueException extends DomainException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}