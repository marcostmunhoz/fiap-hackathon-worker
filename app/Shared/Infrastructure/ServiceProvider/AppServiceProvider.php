<?php

namespace App\Shared\Infrastructure\ServiceProvider;

use App\Shared\Application\Service\EmailNotifierInterface;
use App\Shared\Infrastructure\Service\NotificationEmailNotifier;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Facades\Health;

/**
 * @codeCoverageIgnore
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            EmailNotifierInterface::class,
            NotificationEmailNotifier::class
        );
    }

    public function boot(): void
    {
        Health::checks(
            app()->environment('local')
                ? [DatabaseCheck::new()]
                : [DatabaseCheck::new(), PingCheck::new()->url('https://google.com'),]
        );
    }
}
