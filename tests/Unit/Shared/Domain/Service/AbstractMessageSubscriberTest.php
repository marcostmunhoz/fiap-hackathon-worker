<?php

namespace Tests\Unit\Worker\Domain\Service;

use App\Shared\Domain\Enum\EventType;
use App\Shared\Domain\Service\AbstractMessageSubscriber;
use Exception;
use Mockery;
use Psr\Log\LoggerInterface;
use function Pest\Laravel\instance;

beforeEach(function () {
    $this->loggerMock = Mockery::mock(LoggerInterface::class);
    $this->sut = new class() extends AbstractMessageSubscriber {
        public function getHandlers(): array
        {
            return $this->eventHandlers;
        }

        public function callHandler(EventType $event, array $data): void
        {
            $this->handle($event, $data);
        }

        public function listen(): void
        {
            return;
        }
    };

    instance('log', $this->loggerMock);
});

test('on assigns a handler to the given event', function () {
    // Given
    $event = EventType::VIDEO_UPLOADED;
    $callback = static function () {
        return;
    };

    // When
    $this->sut->on($event, $callback);

    // Then
    expect($this->sut->getHandlers()[EventType::VIDEO_UPLOADED->value])->toBe($callback);
});

test('handle does nothing when there is no handler for the given event', function () {
    // Given
    $event = EventType::VIDEO_UPLOADED;
    $data = ['foo' => 'bar'];
    $called = false;
    $callback = static function () use (&$called) {
        $called = true;
    };

    // When
    $this->sut->callHandler($event, $data);

    // Then
    expect($called)->toBeFalse();
});

test('handle calls the handler for the given event', function () {
    // Given
    $event = EventType::VIDEO_UPLOADED;
    $data = ['foo' => 'bar'];
    $called = false;
    $callback = static function () use (&$called) {
        $called = true;
    };
    $this->sut->on($event, $callback);

    // When
    $this->sut->callHandler($event, $data);

    // Then
    expect($called)->toBeTrue();
});

test('handle logs an error when the handler throws an exception', function () {
    // Given
    $event = EventType::VIDEO_UPLOADED;
    $data = ['foo' => 'bar'];
    $callback = static function () {
        throw new Exception('Something went wrong');
    };
    $this->sut->on($event, $callback);
    $this->loggerMock
        ->expects()
        ->error('Error processing message.', [
            'event' => $event->value,
            'data' => $data,
            'error' => 'Something went wrong',
        ]);

    // When
    $this->sut->callHandler($event, $data);
});