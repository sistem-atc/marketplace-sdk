<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class HttpClientFactory
{
    public static function make(MarketplaceIntegration $integration): PendingRequest
    {
        return Http::withToken($integration->getAccessToken())
            ->baseUrl(config('marketplaces.mercadopago.api_base', 'https://api.mercadopago.com'))
            ->timeout(60);
    }
}
