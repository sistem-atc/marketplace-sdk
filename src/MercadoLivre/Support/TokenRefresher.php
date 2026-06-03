<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreAuthenticationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokenRefresher
{
    private const TOKEN_URL = 'https://api.mercadolivre.com/oauth/token';

    public static function refresh(MarketplaceIntegration $integration): string
    {
        $settings = $integration->getMarketplaceSettings();
        $clientId = $settings['client_id'] ?? null;
        $clientSecret = $settings['client_secret'] ?? null;

        if (!$clientId || !$clientSecret) {
            throw new MercadoLivreAuthenticationException("Integration ML sem client_id/client_secret.");
        }

        $response = Http::asForm()->post(self::TOKEN_URL, [
            'grant_type' => 'refresh_token',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $integration->getRefreshToken(),
        ]);

        if ($response->failed()) {
            Log::error('Mercado Livre token refresh failed', [
                'status' => $response->status(),
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);
            throw new MercadoLivreAuthenticationException('Falha ao renovar token Mercado Livre.');
        }

        $data = $response->json();
        $integration->updateTokens(
            $data['access_token'],
            $data['refresh_token'] ?? $integration->getRefreshToken(),
            $data['expires_in'] ?? 21600
        );

        return $data['access_token'];
    }
}
