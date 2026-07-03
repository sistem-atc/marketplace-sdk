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

        // Auth da API v1 da Loja Integrada:
        //   Authorization: chave_api {chave da API da loja} aplicacao {chave de aplicacao do integrador}
        // settings esperados: api_key (chave da loja) + application_key (integrador).
        // chave_api aceito como alias legado de api_key.
        $apiKey = $settings['api_key'] ?? ($settings['chave_api'] ?? '');
        $applicationKey = $settings['application_key'] ?? '';

        return Http::withHeaders([
            'Authorization' => "chave_api {$apiKey} aplicacao {$applicationKey}",
        ])->baseUrl($settings['api_base'] ?? config('marketplaces.lojaintegrada.api_base', 'https://api.awsli.com.br/v1'))
          ->timeout(30);
    }
}
