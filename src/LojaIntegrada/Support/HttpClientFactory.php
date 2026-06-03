<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class HttpClientFactory
{
    public static function make(MarketplaceIntegration $integration): PendingRequest
    {
        $settings = $integration->getMarketplaceSettings();
        $apiKey = $settings['api_key'] ?? '';
        $chaveApi = $settings['chave_api'] ?? '';

        return Http::withHeaders([
            'X-API-Key' => $apiKey,
            'Authorization' => "Chave {$chaveApi}",
        ])->baseUrl(config('marketplaces.lojaintegrada.api_base', 'https://api.awsli.com.br/v1'))
          ->timeout(30);
    }
}
