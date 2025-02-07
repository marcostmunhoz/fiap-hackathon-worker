<?php

namespace Tests\Unit\Worker\Application\UseCase;

use App\Worker\Application\DTO\ExtractFramesFromUploadedVideoInput;
use App\Worker\Application\UseCase\ExtractFramesFromUploadedVideoUseCase;
use App\Worker\Domain\Enum\FrameLength;
use App\Worker\Domain\Service\VideoFrameGeneratorInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use Mockery;

beforeEach(function () {
    $this->videoFrameGeneratorMock = Mockery::mock(VideoFrameGeneratorInterface::class);
    $this->filesystemMock = Mockery::mock(Filesystem::class);
    $this->sut = new ExtractFramesFromUploadedVideoUseCase($this->videoFrameGeneratorMock, $this->filesystemMock);
});

it('stores frames zip file in storage with correct filename pattern', function () {
    // Given
    $input = new ExtractFramesFromUploadedVideoInput('video.mp4');
    $this->filesystemMock
        ->expects()
        ->readStream('video.mp4')
        ->andReturn('stream');
    $this->videoFrameGeneratorMock
        ->expects()
        ->extractFramesToZip(
            Mockery::type('string'),
            Mockery::type('string'),
            FrameLength::EVERY_FIVE_SECONDS
        );
    $this->filesystemMock
        ->expects()
        ->put('video-frames.zip', Mockery::type('resource'));

    // When
    $this->sut->execute($input);
});