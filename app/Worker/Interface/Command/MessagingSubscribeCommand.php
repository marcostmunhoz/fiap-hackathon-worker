<?php

namespace App\Worker\Interface\Command;

use App\Shared\Domain\Enum\EventType;
use App\Shared\Domain\ValueObject\Email;
use App\Worker\Application\DTO\ExtractFramesFromUploadedVideoInput;
use App\Worker\Application\UseCase\ExtractFramesFromUploadedVideoUseCase;
use App\Worker\Domain\Service\MessageSubscriberInterface;
use App\Worker\Interface\Job\QueueableUseCaseJob;
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

    public function handle(): void
    {
        $this->info('Subscribing to messaging service...');

        $this->subscriber->on(EventType::VIDEO_UPLOADED, function (array $data) {
            $filename = $data['filename'] ?? null;
            $email = $data['user_email'] ?? null;
            $name = $data['user_name'] ?? null;

            if (!$filename || !$email || !$name) {
                throw new InvalidArgumentException('Missing event data.');
            }

            dispatch(
                new QueueableUseCaseJob(
                    ExtractFramesFromUploadedVideoUseCase::class,
                    new ExtractFramesFromUploadedVideoInput(
                        $filename,
                        new Email($email),
                        $name
                    )
                )
            );
        });

        $this->subscriber->listen();
    }
}