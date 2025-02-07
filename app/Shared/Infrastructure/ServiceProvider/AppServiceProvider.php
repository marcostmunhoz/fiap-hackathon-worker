<?php

namespace App\Shared\Infrastructure\ServiceProvider;

use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Facades\Health;

/**
 * @codeCoverageIgnore
 */
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Health::checks(
            app()->environment('local')
                ? [DatabaseCheck::new()]
                : [DatabaseCheck::new(), PingCheck::new()->url('https://google.com'),]
        );
    }
}
