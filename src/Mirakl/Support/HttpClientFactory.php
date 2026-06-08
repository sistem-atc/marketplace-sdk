<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Mirakl\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Cliente HTTP base Mirakl (Seller API). Auth = front API key ESTATICA no
 * header `Authorization: {key}` (literal, SEM "Bearer"; nao expira).
 *
 * Convencao de credencial (padrao dos demais conectores):
 *   - getAccessToken()        = front API key do seller (coluna access_token)
 *   - settings['base_url']    = host do operador Mirakl (ex Centauro)
 *     (fallback settings['api_url'] por compat)
 */
class HttpClientFactory
{
    public static function make(MarketplaceIntegration $integration): PendingRequest
    {
        $settings = $integration->getMarketplaceSettings();
        $apiKey = (string) ($integration->getAccessToken() ?? ($settings['api_key'] ?? ''));
        $baseUrl = (string) ($settings['base_url'] ?? ($settings['api_url'] ?? ''));

        return Http::baseUrl(rtrim($baseUrl, '/'))
            ->withHeaders([
                'Authorization' => $apiKey,
                'Accept' => 'application/json',
                'User-Agent' => 'SistemAtcMarketplaces/1.0 (Mirakl)',
            ])
            ->timeout(60)
            ->connectTimeout(10);
    }
}
