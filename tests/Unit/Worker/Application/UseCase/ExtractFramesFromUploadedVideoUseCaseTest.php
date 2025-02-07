<?php

namespace Tests\Unit\Worker\Application\UseCase;

use App\Shared\Application\Service\EmailNotifierInterface;
use App\Shared\Domain\ValueObject\Email;
use App\Worker\Application\DTO\ExtractFramesFromUploadedVideoInput;
use App\Worker\Application\UseCase\ExtractFramesFromUploadedVideoUseCase;
use App\Worker\Domain\Enum\FrameLength;
use App\Worker\Domain\Service\VideoFrameGeneratorInterface;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Mockery;

beforeEach(function () {
    $this->videoFrameGeneratorMock = Mockery::mock(VideoFrameGeneratorInterface::class);
    $this->filesystemMock = Mockery::mock(Filesystem::class);
    $this->emailNotifierMock = Mockery::mock(EmailNotifierInterface::class);
    $this->sut = new ExtractFramesFromUploadedVideoUseCase(
        $this->videoFrameGeneratorMock,
        $this->filesystemMock,
        $this->emailNotifierMock
    );
});

it('stores frames zip file in storage with correct filename pattern', function () {
    // Given
    $input = new ExtractFramesFromUploadedVideoInput(
        'video.mp4',
        new Email(fake()->safeEmail()),
        fake()->name()
    );
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
    $this->emailNotifierMock
        ->expects()
        ->notify(
            $input->email,
            'Video processing completed',
            'The video processing completed successfully. You can download the frames now.'
        );

    // When
    $this->sut->execute($input);
});

it('notifies user when video processing fails', function () {
    // Given
    $input = new ExtractFramesFromUploadedVideoInput(
        'video.mp4',
        new Email(fake()->safeEmail()),
        fake()->name()
    );
    $this->filesystemMock
        ->expects()
        ->readStream('video.mp4')
        ->andThrow(new Exception('Some error.'));
    $this->emailNotifierMock
        ->expects()
        ->notify(
            $input->email,
            'Video processing failed',
            'The video processing failed. Please try again.'
        );

    // When
    $this->sut->execute($input);
})->throws(Exception::class, 'Some error.');