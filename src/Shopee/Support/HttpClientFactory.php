<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeAuthenticationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class HttpClientFactory
{
    public static function make(MarketplaceIntegration $integration): PendingRequest
    {
        if (! $integration->isIntegrationActive()) {
            throw new ShopeeAuthenticationException('Integração Shopee inativa.');
        }

        TokenRefresher::refresh($integration);

        return Http::baseUrl(config('marketplaces.shopee.base_url', 'https://partner.shopeemobile.com'))
            ->timeout(30)
            ->connectTimeout(10)
            ->acceptJson()
            ->asJson();
    }
}
