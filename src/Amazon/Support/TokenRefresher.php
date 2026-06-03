<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TokenRefresher
{
    public static function refresh(MarketplaceIntegration $integration): string
    {
        $settings = $integration->getMarketplaceSettings();
        $clientId = $settings['client_id'] ?? null;
        $clientSecret = $settings['client_secret'] ?? null;
        $tokenUrl = $settings['lwa_endpoint'] ?? config('marketplaces.amazon.lwa_token_url', 'https://api.amazon.com/auth/o2/token');

        if (!$clientId || !$clientSecret) {
            throw new RuntimeException('Amazon integration missing client_id/client_secret.');
        }

        $response = Http::asForm()->post($tokenUrl, [
            'grant_type' => 'refresh_token',
            'refresh_token' => $integration->getRefreshToken(),
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        if ($response->failed()) {
            Log::error('Amazon token refresh failed', [
                'status' => $response->status(),
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);
            throw new RuntimeException('Falha ao renovar token Amazon.');
        }

        $data = $response->json();
        $integration->updateTokens(
            $data['access_token'],
            $data['refresh_token'] ?? $integration->getRefreshToken(),
            (int) ($data['expires_in'] ?? 3600)
        );

        return $data['access_token'];
    }
}
