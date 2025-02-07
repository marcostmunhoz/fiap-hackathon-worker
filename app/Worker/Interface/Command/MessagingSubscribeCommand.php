<?php

namespace App\Worker\Interface\Command;

use App\Shared\Domain\Enum\EventType;
use App\Worker\Application\DTO\ExtractFramesFromUploadedVideoInput;
use App\Worker\Application\UseCase\ExtractFramesFromUploadedVideoUseCase;
use App\Worker\Domain\Service\MessageSubscriberInterface;
use Illuminate\Console\Command;
use InvalidArgumentException;

/**
 * @codeCoverageIgnore
 */
class MessagingSubscribeCommand extends Command
{
    protected $signature = 'messaging:subscribe';
    protected $description = 'Subscribe to messaging service';

    public function __construct(
        private readonly MessageSubscriberInterface $subscriber
    ) {
        parent::__construct();
    }

    public function handle(
        ExtractFramesFromUploadedVideoUseCase $extractFramesFromUploadedVideoUseCase
    ): void {
        $this->info('Subscribing to messaging service...');

        $this->subscriber->on(EventType::VIDEO_UPLOADED, function (array $data) use ($extractFramesFromUploadedVideoUseCase) {
            $filename = $data['filename'] ?? null;

            if (!$filename) {
                throw new InvalidArgumentException('Missing filename in message data.');
            }

            $extractFramesFromUploadedVideoUseCase->execute(
                new ExtractFramesFromUploadedVideoInput($filename)
            );
        });

        $this->subscriber->listen();
    }
}