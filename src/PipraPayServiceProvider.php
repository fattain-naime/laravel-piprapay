<?php

namespace FattainNaime\PipraPay;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use FattainNaime\PipraPay\Http\Controllers\WebhookController;

class PipraPayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/piprapay.php', 'piprapay');

        $this->app->singleton('piprapay', function ($app) {
            return new PipraPayService(
                config('piprapay.api_key'),
                config('piprapay.sandbox_mode'),
                config('piprapay.base_url'),
                config('piprapay.sandbox_base_url')
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/piprapay.php' => config_path('piprapay.php'),
            ], 'config');
        }

        $this->registerWebhookRoute();
    }

    protected function registerWebhookRoute(): void
    {
        $webhookUri = config('piprapay.webhook_uri', '/piprapay/webhook');

        Route::post($webhookUri, [WebhookController::class, 'handle'])
            ->name('piprapay.webhook');
    }
}
