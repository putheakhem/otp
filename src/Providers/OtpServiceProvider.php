<?php

namespace Putheakhem\Otp\Providers;

use Illuminate\Support\ServiceProvider;
use PutheaKhem\Otp\Services\OtpService;

class OtpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('otp', fn(): \PutheaKhem\Otp\Services\OtpService => new OtpService);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/otp/emails'),
        ], 'otp-views');

        $this->publishes([
            __DIR__.'/../Config/otp.php' => config_path('otp.php'),
        ], 'otp-config');

        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
        ], 'otp-migrations');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'otp');
    }
}
