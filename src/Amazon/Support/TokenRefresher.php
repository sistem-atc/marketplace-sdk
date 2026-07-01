<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Ciclo de vida do access_token LWA da Amazon SP-API.
 *
 * access_token TTL 3600s (1h). refresh_token e' longevo e NAO rolling
 * (Amazon so' devolve um novo ocasionalmente — preserva o antigo). Por
 * isso refresh() e' idempotente (skip se token valido) — o Client chama
 * antes de TODA request; sem o guard faria um POST LWA a cada chamada.
 *
 * Credenciais (client_id/secret) sao SEMPRE per-Integration (settings).
 * URL do token endpoint pode vir de settings.lwa_endpoint ou config
 * (URL publica do vendor, nao e' credencial).
 */
class TokenRefresher
{
    /** Margem (segundos) antes do expires_at pra considerar expirado. */
    private const EXPIRY_MARGIN_SECONDS = 300;

    /**
     * Cache em memoria dos tokens grantless por (integration, scope), com
     * validade. Grantless NAO usa refresh_token — e' um token de aplicacao.
     *
     * @var array<string, array{token: string, expires_at: int}>
     */
    private static array $grantlessCache = [];

    /**
     * Token GRANTLESS (grant_type=client_credentials + scope). Usado pelas
     * operacoes SP-API que NAO exigem autorizacao de um seller especifico —
     * ex.: Notifications createDestination/getDestinations/deleteDestination e
     * getSubscriptionById/deleteSubscriptionById.
     *
     * NAO depende de refresh_token; so' de client_id/client_secret da app.
     * Cacheado em memoria por (integration, scope) ate' ~5min antes de expirar.
     *
     * @param  string  $scope  ex.: 'sellingpartnerapi::notifications'
     */
    public static function grantless(MarketplaceIntegration $integration, string $scope, bool $force = false): string
    {
        $cacheKey = $integration->getIntegrationIdentifier().'|'.$scope;
        $cached = self::$grantlessCache[$cacheKey] ?? null;
        if (! $force && $cached && $cached['expires_at'] > (time() + self::EXPIRY_MARGIN_SECONDS)) {
            return $cached['token'];
        }

        [$clientId, $clientSecret, $tokenUrl] = self::resolveLwaCredentials($integration);

        if (! $clientId || ! $clientSecret) {
            throw new RuntimeException('Integration Amazon sem credenciais LWA (client_id/client_secret) pro token grantless.');
        }

        $response = Http::asForm()->timeout(15)->post($tokenUrl, [
            'grant_type'    => 'client_credentials',
            'scope'         => $scope,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
        ]);

        if ($response->failed()) {
            Log::error('Amazon LWA grantless failed', [
                'status'         => $response->status(),
                'scope'          => $scope,
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);
            throw new RuntimeException(
                'LWA grantless failed (scope='.$scope.'): HTTP '.$response->status().' — '.($response->json('error_description') ?? 'unknown')
            );
        }

        $data  = $response->json();
        $token = (string) ($data['access_token'] ?? '');
        if ($token === '') {
            throw new RuntimeException('LWA grantless sem access_token (scope='.$scope.').');
        }

        self::$grantlessCache[$cacheKey] = [
            'token'      => $token,
            'expires_at' => time() + (int) ($data['expires_in'] ?? 3600),
        ];

        return $token;
    }

    /**
     * Garante access_token valido. Idempotente: skip se ainda fresco
     * (a menos que $force). Retorna o token utilizavel.
     */
    public static function refresh(MarketplaceIntegration $integration, bool $force = false): string
    {
        $token = $integration->getAccessToken();

        if (! $force && $token && ! self::isExpired($integration)) {
            return $token;
        }

        if (! $integration->getRefreshToken()) {
            throw new RuntimeException('Integration Amazon sem refresh_token — refazer OAuth consent flow.');
        }

        [$clientId, $clientSecret, $tokenUrl] = self::resolveLwaCredentials($integration);

        if (! $clientId || ! $clientSecret) {
            throw new RuntimeException('Integration Amazon sem credenciais LWA (client_id/client_secret).');
        }

        $response = Http::asForm()->timeout(15)->post($tokenUrl, [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $integration->getRefreshToken(),
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
        ]);

        if ($response->failed()) {
            Log::error('Amazon LWA refresh failed', [
                'status'         => $response->status(),
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);
            throw new RuntimeException(
                'LWA refresh failed: HTTP '.$response->status().' — '.($response->json('error_description') ?? 'unknown')
            );
        }

        $data = $response->json();

        $integration->updateTokens(
            $data['access_token'],
            // Amazon devolve novo refresh_token so' ocasionalmente — preserva o antigo.
            $data['refresh_token'] ?? $integration->getRefreshToken(),
            (int) ($data['expires_in'] ?? 3600),
        );

        return $data['access_token'];
    }

    /**
     * Troca authorization_code (callback OAuth) por refresh_token inicial.
     * NAO persiste — o host decide como salvar (o consent flow precisa do
     * refresh_token + access_token de volta).
     *
     * @return array{access_token: string, refresh_token: string, expires_in: int}
     */
    public static function exchangeAuthorizationCode(MarketplaceIntegration $integration, string $spapiOauthCode): array
    {
        [$clientId, $clientSecret, $tokenUrl] = self::resolveLwaCredentials($integration);

        if (! $clientId || ! $clientSecret) {
            throw new RuntimeException('Integration Amazon sem credenciais LWA antes de autorizar.');
        }

        $response = Http::asForm()->timeout(15)->post($tokenUrl, [
            'grant_type'    => 'authorization_code',
            'code'          => $spapiOauthCode,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
        ]);

        if ($response->failed()) {
            Log::error('Amazon LWA authorization_code exchange failed', [
                'status'         => $response->status(),
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);
            throw new RuntimeException(
                'LWA code exchange failed: HTTP '.$response->status().' — '.($response->json('error_description') ?? 'unknown')
            );
        }

        $data = $response->json();

        if (empty($data['refresh_token'])) {
            throw new RuntimeException('LWA response sem refresh_token.');
        }

        return [
            'access_token'  => (string) $data['access_token'],
            'refresh_token' => (string) $data['refresh_token'],
            'expires_in'    => (int) ($data['expires_in'] ?? 3600),
        ];
    }

    private static function isExpired(MarketplaceIntegration $integration): bool
    {
        if (method_exists($integration, 'isExpired')) {
            return $integration->isExpired();
        }

        $settings  = $integration->getMarketplaceSettings();
        $expiresAt = $settings['expires_at'] ?? null;
        if (! $expiresAt) {
            return false;
        }

        return (new \DateTime($expiresAt))->getTimestamp() < (time() + self::EXPIRY_MARGIN_SECONDS);
    }

    /**
     * @return array{0: string, 1: string, 2: string} [client_id, client_secret, token_url]
     */
    private static function resolveLwaCredentials(MarketplaceIntegration $integration): array
    {
        $settings = $integration->getMarketplaceSettings();

        $clientId     = (string) ($settings['client_id'] ?? '');
        $clientSecret = (string) ($settings['client_secret'] ?? '');
        $tokenUrl     = ! empty($settings['lwa_endpoint'])
            ? (string) $settings['lwa_endpoint']
            : (string) config('marketplaces.amazon.lwa_token_url', 'https://api.amazon.com/auth/o2/token');

        return [$clientId, $clientSecret, $tokenUrl];
    }
}
