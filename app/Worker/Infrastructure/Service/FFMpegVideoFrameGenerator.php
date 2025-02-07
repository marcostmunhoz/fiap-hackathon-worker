<?php

namespace App\Worker\Infrastructure\Service;

use App\Shared\Domain\Data\TemporaryDirectory;
use App\Shared\Domain\Data\TemporaryFile;
use App\Worker\Domain\Enum\FrameLength;
use App\Worker\Domain\Service\VideoFrameGeneratorInterface;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Video;
use InvalidArgumentException;
use ZipArchive;

readonly class FFMpegVideoFrameGenerator implements VideoFrameGeneratorInterface
{
    private FFMpeg $instance;

    public function __construct()
    {
        $this->instance = FFMpeg::create();
    }

    public function extractFramesToZip(
        string $path,
        string $destinationPath,
        FrameLength $length = FrameLength::EVERY_MINUTE
    ): void {
        if (!str_ends_with($destinationPath, '.zip')) {
            throw new InvalidArgumentException('Destination path must be a zip file.');
        }

        $temporaryDirectory = new TemporaryDirectory();
        $this->extractFrames($path, $temporaryDirectory->getPath(), $length);
        $this->addFramesToZip($temporaryDirectory->getPath(), $destinationPath);
    }

    private function extractFrames(string $path, string $destinationDirectory, FrameLength $length): void
    {
        $ffmpegFrameLength = "1/{$length->value}";
        $temporaryFile = new TemporaryFile('mp4');

        /** @var Video $video */
        $video = $this->instance->open($path);
        $video->filters()
            ->extractMultipleFrames($ffmpegFrameLength, $destinationDirectory)
            ->synchronize();
        $video->save(new X264(), $temporaryFile->getPath());
    }

    private function addFramesToZip(string $directory, string $destinationPath): void
    {
        $zip = new ZipArchive();
        $zip->open($destinationPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach (glob("{$directory}/*") as $frame) {
            $zip->addFile($frame, basename($frame));
        }

        $zip->close();
    }
}