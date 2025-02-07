<?php

namespace App\Worker\Application\DTO;

use App\Shared\Domain\ValueObject\Email;

readonly class ExtractFramesFromUploadedVideoInput
{
    public function __construct(
        public string $filename,
        public Email $email,
        public string $name
    ) {
    }
}