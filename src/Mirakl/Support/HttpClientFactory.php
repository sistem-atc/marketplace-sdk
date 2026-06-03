<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Mirakl\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class HttpClientFactory
{
    public static function make(MarketplaceIntegration $integration): PendingRequest
    {
        $settings = $integration->getMarketplaceSettings();
        $apiKey = $settings['api_key'] ?? '';
        $apiUrl = $settings['api_url'] ?? ''; // e.g. https://...mirakl.net/api

        return Http::withHeaders([
            'Authorization' => $apiKey,
        ])->baseUrl($apiUrl)->timeout(30);
    }
}
