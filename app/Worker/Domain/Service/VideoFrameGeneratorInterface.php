<?php

namespace App\Worker\Domain\Service;

use App\Worker\Domain\Enum\FrameLength;

interface VideoFrameGeneratorInterface
{
    public function extractFramesToZip(
        string $path,
        string $destinationPath,
        FrameLength $length = FrameLength::EVERY_MINUTE
    ): void;
}