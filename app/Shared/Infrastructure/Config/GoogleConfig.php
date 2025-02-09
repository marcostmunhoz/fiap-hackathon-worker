<?php

namespace App\Shared\Infrastructure\Config;

use Illuminate\Contracts\Config\Repository;

/**
 * @codeCoverageIgnore
 */
class GoogleConfig
{
    public function __construct(
        private readonly Repository $config
    ) {
    }

    public function getProjectId(): string
    {
        return $this->config->get('services.google.project_id');
    }

    public function getPubSubServiceAccountKeyPath(): string
    {
        return $this->config->get('services.google.pubsub.service_account_key_path');
    }

    public function getPubSubSubscriptionId(): string
    {
        return $this->config->get('services.google.pubsub.subscription_id');
    }
}