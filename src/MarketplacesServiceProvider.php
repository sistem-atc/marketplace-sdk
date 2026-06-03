<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class MarketplacesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/marketplaces.php', 'marketplaces'
        );
    }

    public function boot(): void
    {
        $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/marketplaces.php' => config_path('marketplaces.php'),
            ], 'marketplaces-config');
        }
    }

    protected function registerRoutes(): void
    {
        if (config('marketplaces.webhooks.enabled', true)) {
            Route::prefix(config('marketplaces.webhooks.prefix', 'api/webhooks'))
                ->middleware(config('marketplaces.webhooks.middleware', ['api']))
                ->group(function () {
                    // Agora a rota é direto /{marketplace} sob o prefixo api/webhooks
                    Route::match(['GET', 'POST'], '/{marketplace}', [\SistemAtc\Marketplaces\Http\Controllers\WebhookController::class, 'handle'])
                        ->name('marketplaces.webhooks');
                });
        }
    }
}
