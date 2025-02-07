<?php

namespace App\Worker\Application\UseCase;

use App\Shared\Domain\Data\TemporaryFile;
use App\Worker\Application\DTO\ExtractFramesFromUploadedVideoInput;
use App\Worker\Domain\Enum\FrameLength;
use App\Worker\Domain\Service\VideoFrameGeneratorInterface;
use Illuminate\Contracts\Filesystem\Filesystem;

readonly class ExtractFramesFromUploadedVideoUseCase
{
    public function __construct(
        private VideoFrameGeneratorInterface $videoFrameGenerator,
        private Filesystem $filesystem
    ) {
    }

    public function execute(ExtractFramesFromUploadedVideoInput $input): void
    {
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
    }
}