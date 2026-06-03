<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Tiktok\Exceptions\TiktokAuthenticationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class HttpClientFactory
{
    public static function make(MarketplaceIntegration $integration): PendingRequest
    {
        if (! $integration->isIntegrationActive()) {
            throw new TiktokAuthenticationException('Integração TikTok inativa.');
        }

        TokenRefresher::refresh($integration);

        return Http::baseUrl(config('marketplaces.tiktok.base_url', 'https://open-api.tiktokglobalshop.com'))
            ->timeout(30)
            ->connectTimeout(10)
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'x-tts-access-token' => $integration->getAccessToken() ?? '',
            ]);
    }
}
