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
        $prefix = config('marketplaces.webhooks.prefix', 'api/webhooks');
        $middleware = config('marketplaces.webhooks.middleware', ['api']);
        $controller = \SistemAtc\Marketplaces\Http\Controllers\WebhookController::class;

        // Modo PER-MP (recomendado): registra uma rota dedicada por marketplace
        // listado em `marketplaces.webhooks.routes`. Permite migrar os conectores
        // pro pacote UM A UM — o host mantem no proprio routes/ os que ainda nao
        // migraram, sem colisao com um catch-all.
        $routes = (array) config('marketplaces.webhooks.routes', []);

        if (! empty($routes)) {
            Route::prefix($prefix)->middleware($middleware)->group(function () use ($routes, $controller) {
                foreach ($routes as $marketplace) {
                    Route::match(['GET', 'POST'], "/{$marketplace}", [$controller, 'handle'])
                        ->defaults('marketplace', $marketplace)
                        ->name("marketplaces.webhooks.{$marketplace}");
                }
            });
        }

        // Modo CATCH-ALL (legado): so' quando explicitamente habilitado E sem
        // lista per-MP. Mantido por compatibilidade; nao usar junto com `routes`.
        if (config('marketplaces.webhooks.enabled', false) && empty($routes)) {
            Route::prefix($prefix)->middleware($middleware)->group(function () use ($controller) {
                Route::match(['GET', 'POST'], '/{marketplace}', [$controller, 'handle'])
                    ->name('marketplaces.webhooks');
            });
        }
    }
}
