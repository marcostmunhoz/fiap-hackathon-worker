<?php

namespace Tests\Unit\Shared\Infrastructure\Service;

use App\Shared\Infrastructure\Config\GoogleConfig;
use App\Shared\Infrastructure\Service\PubSubSubscriptionResolver;
use Google\Cloud\PubSub\Subscription;
use Mockery;

it('resolves the pubsub subscription from the client using project id and credentials', function () {
    // Given
    $pubSubClientMock = Mockery::mock('overload:Google\Cloud\PubSub\PubSubClient');
    $subscription = Mockery::mock(Subscription::class);
    $googleConfigMock = Mockery::mock(GoogleConfig::class);
    $googleConfigMock->shouldReceive('getProjectId')
        ->andReturn('project-id');
    $googleConfigMock->shouldReceive('getPubSubServiceAccountKeyPath')
        ->andReturn('service-account-key-path');
    $googleConfigMock->shouldReceive('getPubSubSubscriptionId')
        ->andReturn('subscription-id');
    $pubSubClientMock->shouldReceive('__construct')
        ->with([
            'projectId' => 'project-id',
            'credentials' => 'service-account-key-path',
        ]);
    $pubSubClientMock->shouldReceive('subscription')
        ->with('subscription-id')
        ->andReturn($subscription);
    $sut = new PubSubSubscriptionResolver($googleConfigMock);

    // When
    $result = $sut->resolve();

    // Then
    expect($result)->toBe($subscription);
});