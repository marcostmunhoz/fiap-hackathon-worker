<?php

namespace App\Worker\Application\UseCase;

use App\Shared\Application\Service\EmailNotifierInterface;
use App\Shared\Domain\Data\TemporaryFile;
use App\Worker\Application\DTO\ExtractFramesFromUploadedVideoInput;
use App\Worker\Domain\Enum\FrameLength;
use App\Worker\Domain\Service\VideoFrameGeneratorInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use Throwable;

readonly class ExtractFramesFromUploadedVideoUseCase
{
    public function __construct(
        private VideoFrameGeneratorInterface $videoFrameGenerator,
        private Filesystem $filesystem,
        private EmailNotifierInterface $emailNotifier
    ) {
    }

    public function execute(ExtractFramesFromUploadedVideoInput $input): void
    {
        try {
            $filename = $input->filename;
            /** @var string $fileExtension */
            $fileExtension = pathinfo($filename, PATHINFO_EXTENSION) ?: 'mp4';
            /** @var string $baseFilename */
            $baseFilename = pathinfo($filename, PATHINFO_FILENAME);
            $tempSourceFile = new TemporaryFile($fileExtension);
            $tempDestinationFile = new TemporaryFile('zip');

            $tempSourceFile->put($this->filesystem->readStream($filename));

            $this->videoFrameGenerator->extractFramesToZip(
                $tempSourceFile->getPath(),
                $tempDestinationFile->getPath(),
                FrameLength::EVERY_FIVE_SECONDS
            );

            $this->filesystem->put("{$baseFilename}-frames.zip", $tempDestinationFile->readAsStream());

            $this->emailNotifier->notify(
                $input->email,
                'Video processing completed',
                'The video processing completed successfully. You can download the frames now.'
            );
        } catch (Throwable $e) {
            $this->emailNotifier->notify(
                $input->email,
                'Video processing failed',
                'The video processing failed. Please try again.'
            );

            throw $e;
        }
    }
}