<?php

namespace App\Shared\Domain\Data;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * @codeCoverageIgnore
 */
readonly class TemporaryFile
{
    private Filesystem $disk;
    private string $path;

    public function __construct(string $extension)
    {
        $this->disk = Storage::disk('temporary');
        $this->path = uniqid('temp_file_', true).'.'.$extension;
        $this->createTemporaryFile();
    }

    public function __destruct()
    {
        $this->deleteTemporaryFile();
    }

    /**
     * @param string|resource $contentsOrResource
     */
    public function put(mixed $contentsOrResource): void
    {
        $this->disk->put($this->path, $contentsOrResource);
    }

    /**
     * @return resource
     */
    public function readAsStream(): mixed
    {
        $stream = $this->disk->readStream($this->path);

        if ($stream === null) {
            throw new RuntimeException('Could not read temporary file.');
        }

        return $stream;
    }

    public function getPath(): string
    {
        return $this->disk->path($this->path);
    }

    private function createTemporaryFile(): void
    {
        $created = $this->disk->put($this->path, '');

        if (!$created) {
            throw new RuntimeException('Could not create temporary file.');
        }
    }

    private function deleteTemporaryFile(): void
    {
        if (!$this->disk->exists($this->path)) {
            return;
        }

        $this->disk->delete($this->path);
    }
}