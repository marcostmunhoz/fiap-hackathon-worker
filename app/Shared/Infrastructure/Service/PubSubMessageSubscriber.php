<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Enum\EventType;
use App\Shared\Domain\Service\AbstractMessageSubscriber;
use RuntimeException;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class PubSubMessageSubscriber extends AbstractMessageSubscriber
{
    public function __construct(
        private readonly PubSubSubscriptionResolver $resolver
    ) {
    }

    public function listen(): void
    {
        $subscription = $this->resolver->resolve();

        while (true) {
            $messages = $subscription->pull();

            foreach ($messages as $message) {
                $data = json_decode($message->data(), true, 512, JSON_THROW_ON_ERROR);

                logger()->info("Processing message {$message->id()}...");

                try {
                    if (!isset($data['event'], $data['data'])) {
                        throw new RuntimeException('Invalid message format.');
                    }

                    $this->handle(EventType::from($data['event']), $data['data']);

                    logger()->info("Message {$message->id()} processed.");
                } catch (Throwable $e) {
                    logger()->error("Error processing message {$message->id()}: {$e->getMessage()}");
                } finally {
                    $subscription->acknowledge($message);
                }
            }
        }
    }
}