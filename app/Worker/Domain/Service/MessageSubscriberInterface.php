<?php

namespace App\Worker\Domain\Service;

use App\Shared\Domain\Enum\EventType;

/**
 * @phpstan-type TCallbackParam array<array-key, mixed>
 */
interface MessageSubscriberInterface
{
    public function listen(): void;

    /**
     * @param callable(TCallbackParam): void $callback
     */
    public function on(EventType $event, callable $callback): void;
}