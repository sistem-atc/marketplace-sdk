<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Magalu\Exceptions\MagaluAuthenticationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class HttpClientFactory
{
    public static function make(MarketplaceIntegration $integration): PendingRequest
    {
        if (! $integration->isIntegrationActive()) {
            throw new MagaluAuthenticationException('Integração Magalu inativa.');
        }

        TokenRefresher::refresh($integration);

        $settings = $integration->getMarketplaceSettings();
        $tenantId = $settings['tenant_id'] ?? null;

        if (! $tenantId) {
            throw new MagaluAuthenticationException('tenant_id ausente nas configurações da Magalu.');
        }

        return Http::baseUrl(config('marketplaces.magalu.api_base', 'https://api.magalu.com'))
            ->withToken($integration->getAccessToken())
            ->withHeaders(['X-Tenant-Id' => $tenantId])
            ->timeout(180)
            ->connectTimeout(10)
            ->acceptJson();
    }
}
