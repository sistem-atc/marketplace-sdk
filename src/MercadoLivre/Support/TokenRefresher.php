<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreAuthenticationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokenRefresher
{
    private const TOKEN_URL = 'https://api.mercadolibre.com/oauth/token';

    /** Margem de segurança (segundos) antes do expires_at para considerar expirado. */
    private const EXPIRY_MARGIN_SECONDS = 300;

    public static function refresh(MarketplaceIntegration $integration): string
    {
        $token = $integration->getAccessToken();

        // Skipa refresh se o token ainda é válido (com margem de segurança).
        // Evita chamada desnecessária ao endpoint de token em cada request.
        if ($token && ! self::isExpired($integration)) {
            return $token;
        }

        $settings     = $integration->getMarketplaceSettings();
        $clientId     = $settings['client_id'] ?? null;
        $clientSecret = $settings['client_secret'] ?? null;

        if (! $clientId || ! $clientSecret) {
            throw new MercadoLivreAuthenticationException('Integration ML sem client_id/client_secret nas settings.');
        }

        $response = Http::asForm()->post(self::TOKEN_URL, [
            'grant_type'    => 'refresh_token',
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $integration->getRefreshToken(),
        ]);

        if ($response->failed()) {
            Log::error('Mercado Livre token refresh failed', [
                'status'         => $response->status(),
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);
            throw new MercadoLivreAuthenticationException('Falha ao renovar token Mercado Livre: '.$response->body());
        }

        $data = $response->json();
        $integration->updateTokens(
            $data['access_token'],
            $data['refresh_token'] ?? $integration->getRefreshToken(),
            $data['expires_in'] ?? 21600,
        );

        return $data['access_token'];
    }

    private static function isExpired(MarketplaceIntegration $integration): bool
    {
        // Tenta pegar expires_at via método do contrato ou propriedade.
        // O contrato não expõe expires_at diretamente — verificamos via
        // getMarketplaceSettings ou cast de datetime se disponível.
        // Como o host implementa updateTokens com expires_at, o model
        // expõe a data via Eloquent. Usamos reflection defensiva.
        if (method_exists($integration, 'isExpired')) {
            return $integration->isExpired(); // host Integration::isExpired()
        }

        $settings  = $integration->getMarketplaceSettings();
        $expiresAt = $settings['expires_at'] ?? null;
        if (! $expiresAt) {
            return false; // Sem data → assume válido
        }

        return (new \DateTime($expiresAt))->getTimestamp()
            < (time() + self::EXPIRY_MARGIN_SECONDS);
    }
}
