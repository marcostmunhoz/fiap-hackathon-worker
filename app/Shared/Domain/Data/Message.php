<?php

namespace App\Shared\Domain\Data;

use App\Shared\Domain\Enum\EventType;
use App\Shared\Domain\Exception\InvalidValueException;
use App\Shared\Domain\Service\MessageSubscriberInterface;

/**
 * @codeCoverageIgnore
 *
 * @phpstan-import-type TCallbackParam from MessageSubscriberInterface
 */
readonly class Message
{
    /**
     * @param EventType      $event
     * @param TCallbackParam $data
     */
    public function __construct(
        public EventType $event,
        public array $data,
    ) {
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $event = $data['event'] ?? null;

        if (!$event) {
            throw new InvalidValueException('Invalid message event type.');
        }

        return new self(EventType::from($event), $data['data'] ?? []);
    }
}