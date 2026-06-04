<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreAuthenticationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class HttpClientFactory
{
    public static function make(MarketplaceIntegration $integration): PendingRequest
    {
        if (! $integration->isIntegrationActive()) {
            throw new MercadoLivreAuthenticationException('Integração Mercado Livre inativa.');
        }

        // A logica de refresh sera movida para um TokenRefresher que usa o Contract
        TokenRefresher::refresh($integration);

        return Http::baseUrl(config('marketplaces.mercadolivre.api_base', 'https://api.mercadolibre.com'))
            ->withToken($integration->getAccessToken())
            ->timeout(60)
            ->connectTimeout(10)
            ->acceptJson();
    }
}
