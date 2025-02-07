<?php

namespace App\Worker\Application\DTO;

readonly class ExtractFramesFromUploadedVideoInput
{
    public function __construct(
        public string $filename
    ) {
    }
}