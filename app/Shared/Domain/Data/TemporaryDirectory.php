<?php

namespace App\Shared\Domain\Data;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * @codeCoverageIgnore
 */
readonly class TemporaryDirectory
{
    private string $path;
    private Filesystem $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('temporary');
        $this->path = uniqid('temp_directory_', true);
        $this->createTemporaryDirectory();
    }

    public function __destruct()
    {
        $this->deleteTemporaryDirectory();
    }

    public function getPath(): string
    {
        return $this->disk->path($this->path);
    }

    private function createTemporaryDirectory(): void
    {
        $created = $this->disk->makeDirectory($this->path);

        if (!$created) {
            throw new RuntimeException('Could not create temporary directory.');
        }
    }

    private function deleteTemporaryDirectory(): void
    {
        $this->disk->deleteDirectory($this->path);
    }
}