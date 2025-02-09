<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Infrastructure\Config\GoogleConfig;
use Google\Cloud\PubSub\PubSubClient;
use Google\Cloud\PubSub\Subscription;

readonly class PubSubSubscriptionResolver
{
    public function __construct(
        private GoogleConfig $config
    ) {
    }

    public function resolve(): Subscription
    {
        $client = new PubSubClient([
            'projectId' => $this->config->getProjectId(),
            'credentials' => $this->config->getPubSubServiceAccountKeyPath(),
        ]);

        return $client->subscription($this->config->getPubSubSubscriptionId());
    }
}