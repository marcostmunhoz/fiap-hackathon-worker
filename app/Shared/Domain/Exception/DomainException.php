<?php

namespace App\Shared\Domain\Exception;

use RuntimeException;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class DomainException extends RuntimeException
{
    public function __construct(
        string $message,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }
}