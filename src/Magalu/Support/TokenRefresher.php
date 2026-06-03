<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Magalu\Exceptions\MagaluAuthenticationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokenRefresher
{
    public static function refresh(MarketplaceIntegration $integration): string
    {
        $settings = $integration->getMarketplaceSettings();
        $clientId = $settings['client_id'] ?? null;
        $clientSecret = $settings['client_secret'] ?? null;
        $tokenUrl = config('marketplaces.magalu.token_url', 'https://autoseg-idp.luizalabs.com/oauth/token');

        if (!$clientId || !$clientSecret) {
            throw new MagaluAuthenticationException("Integration Magalu sem client_id/client_secret.");
        }

        $response = Http::asForm()->post($tokenUrl, [
            'grant_type' => 'refresh_token',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $integration->getRefreshToken(),
        ]);

        if ($response->failed()) {
            Log::error('Magalu token refresh failed', [
                'status' => $response->status(),
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);
            throw new MagaluAuthenticationException('Falha ao renovar token Magalu.');
        }

        $data = $response->json();
        $integration->updateTokens(
            $data['access_token'],
            $data['refresh_token'],
            $data['expires_in'] ?? 7200
        );

        return $data['access_token'];
    }
}
