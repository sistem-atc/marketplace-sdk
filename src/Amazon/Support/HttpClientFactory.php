<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class HttpClientFactory
{
    public static function make(MarketplaceIntegration $integration): PendingRequest
    {
        // Aqui o host (Bunker) deve garantir que o token esta fresco antes de chamar.
        // Mas se precisar de refresh auto:
        // TokenRefresher::refresh($integration);

        $settings = $integration->getMarketplaceSettings();
        $base = $settings['endpoint'] ?? config('marketplaces.amazon.spapi_base_url', 'https://sellingpartnerapi-na.amazon.com');

        return Http::withHeaders([
            'x-amz-access-token' => $integration->getAccessToken(),
            'Accept' => 'application/json',
            'User-Agent' => 'SistemAtcMarketplaces/1.0',
        ])->baseUrl($base)->timeout(30);
    }
}
