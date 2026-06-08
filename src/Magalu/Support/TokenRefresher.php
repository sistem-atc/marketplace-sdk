<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Magalu\Exceptions\MagaluAuthenticationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Refresh do access_token Magalu Marketplace.
 *
 * Magalu: access_token expira em 7200s (2h); refresh_token e' rolling (ate'
 * 180 dias, cada refresh gera um novo). Por isso 2 cuidados sao obrigatorios:
 *
 *   1. Idempotencia (skip se token ainda valido) — o HttpClientFactory chama
 *      refresh() ANTES de TODA chamada de API. Sem o guard, cada request
 *      faria um POST no endpoint de token e rotacionaria o refresh_token.
 *   2. Lock — como o refresh_token e' rolling, duas chamadas concorrentes que
 *      passem o guard ao mesmo tempo correriam pra rotacionar, invalidando o
 *      refresh_token uma da outra. O lock serializa o refresh por integration.
 */
class TokenRefresher
{
    /** Margem de seguranca (segundos) antes do expires_at pra considerar expirado. */
    private const EXPIRY_MARGIN_SECONDS = 300;

    public static function refresh(MarketplaceIntegration $integration): string
    {
        $token = $integration->getAccessToken();

        // Skipa refresh se o token ainda e' valido (com margem). Evita chamada
        // desnecessaria ao endpoint de token a cada request.
        if ($token && ! self::isExpired($integration)) {
            return $token;
        }

        $lock = Cache::lock('magalu_token_refresh_'.$integration->getIntegrationIdentifier(), 15);

        try {
            $lock->block(10);

            // Double-check apos adquirir o lock: outra request pode ter
            // renovado enquanto esperavamos.
            $token = $integration->getAccessToken();
            if ($token && ! self::isExpired($integration)) {
                return $token;
            }

            return self::performRefresh($integration);
        } finally {
            optional($lock)->release();
        }
    }

    private static function performRefresh(MarketplaceIntegration $integration): string
    {
        $settings     = $integration->getMarketplaceSettings();
        $clientId     = $settings['client_id'] ?? null;
        $clientSecret = $settings['client_secret'] ?? null;
        $tokenUrl     = config('marketplaces.magalu.token_url', 'https://autoseg-idp.luizalabs.com/oauth/token');

        if (! $clientId || ! $clientSecret) {
            throw new MagaluAuthenticationException('Integration Magalu sem client_id/client_secret nas settings.');
        }

        $response = Http::asForm()->post($tokenUrl, [
            'grant_type'    => 'refresh_token',
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $integration->getRefreshToken(),
        ]);

        if ($response->failed()) {
            Log::error('Magalu token refresh failed', [
                'status'         => $response->status(),
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);
            throw new MagaluAuthenticationException('Falha ao renovar token Magalu: '.$response->body());
        }

        $data = $response->json();

        if (empty($data['access_token']) || empty($data['refresh_token'])) {
            throw new MagaluAuthenticationException('Resposta Magalu sem access_token/refresh_token.');
        }

        $integration->updateTokens(
            $data['access_token'],
            $data['refresh_token'],
            $data['expires_in'] ?? 7200,
        );

        return $data['access_token'];
    }

    private static function isExpired(MarketplaceIntegration $integration): bool
    {
        // O host implementa Integration::isExpired() (com margem propria).
        if (method_exists($integration, 'isExpired')) {
            return $integration->isExpired();
        }

        $settings  = $integration->getMarketplaceSettings();
        $expiresAt = $settings['expires_at'] ?? null;
        if (! $expiresAt) {
            return false; // Sem data → assume valido.
        }

        return (new \DateTime($expiresAt))->getTimestamp()
            < (time() + self::EXPIRY_MARGIN_SECONDS);
    }
}
