<?php

namespace App\Worker\Domain\Service;

use App\Shared\Domain\Enum\EventType;
use Throwable;

/**
 * @phpstan-import-type TCallbackParam from MessageSubscriberInterface
 */
abstract class AbstractMessageSubscriber implements MessageSubscriberInterface
{
    /** @var array<EventType, (callable(TCallbackParam): void)> */
    protected array $eventHandlers = [];

    public function on(EventType $event, callable $callback): void
    {
        $this->eventHandlers[$event->value] = $callback;
    }

    /**
     * @param TCallbackParam $data
     */
    protected function handle(EventType $event, array $data): void
    {
        try {
            $handler = $this->eventHandlers[$event->value] ?? null;

            if (!$handler) {
                return;
            }

            $handler($data);
        } catch (Throwable $e) {
            logger()->error('Error processing message.', [
                'event' => $event->value,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
        }
    }
}