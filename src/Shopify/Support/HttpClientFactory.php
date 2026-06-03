<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class HttpClientFactory
{
    public static function make(MarketplaceIntegration $integration): PendingRequest
    {
        $settings = $integration->getMarketplaceSettings();
        $shop = $settings['shop_domain'] ?? ''; // e.g. store-name.myshopify.com

        return Http::withHeaders([
            'X-Shopify-Access-Token' => $integration->getAccessToken(),
        ])->baseUrl("https://{$shop}/admin/api/".config('marketplaces.shopify.api_version', '2024-04'))->timeout(30);
    }
}
