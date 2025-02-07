<?php

namespace App\Worker\Infrastructure\ServiceProvider;

use App\Worker\Application\UseCase\ExtractFramesFromUploadedVideoUseCase;
use App\Worker\Domain\Service\MessageSubscriberInterface;
use App\Worker\Domain\Service\VideoFrameGeneratorInterface;
use App\Worker\Infrastructure\Service\DatabaseMessageSubscriber;
use App\Worker\Infrastructure\Service\FFMpegVideoFrameGenerator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

/**
 * @codeCoverageIgnore
 */
class WorkerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MessageSubscriberInterface::class, function () {
            return new DatabaseMessageSubscriber();
        });

        $this->app->bind(VideoFrameGeneratorInterface::class, function () {
            return new FFMpegVideoFrameGenerator();
        });

        $this->app->when(ExtractFramesFromUploadedVideoUseCase::class)
            ->needs(Filesystem::class)
            ->give(static fn () => Storage::disk('videos'));
    }
}