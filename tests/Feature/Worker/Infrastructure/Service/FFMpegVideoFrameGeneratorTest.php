<?php

namespace Tests\Feature\Worker\Infrastructure\Service;

use App\Worker\Infrastructure\Service\FFMpegVideoFrameGenerator;
use InvalidArgumentException;

beforeEach(function () {
    $this->outputFilePath = __DIR__.'/output.zip';
    $this->sut = new FFMpegVideoFrameGenerator();
});

afterEach(function () {
    if (file_exists($this->outputFilePath)) {
        unlink($this->outputFilePath);
    }
});

test('extractFramesToZip throws when provided destination path is not a zip file', function () {
    // Given
    $path = 'path/to/video.mp4';
    $destinationPath = 'path/to/frames.rar';

    // When
    $this->sut->extractFramesToZip($path, $destinationPath);
})->throws(InvalidArgumentException::class, 'Destination path must be a zip file.');

test('extractFramesToZip extracts frames from video and adds them to a zip file', function () {
    // Given
    $path = base_path('tests/fixtures/sample.mp4');

    // When
    $this->sut->extractFramesToZip($path, $this->outputFilePath);

    // Then
    $this->assertFileExists($this->outputFilePath);
});