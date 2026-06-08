<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Tiktok\Exceptions\TiktokAuthenticationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Refresh do access_token TikTok Shop (Auth API global auth.tiktok-shops.com).
 *
 * Tokens TikTok sao ROLLING (access + refresh rotacionam a cada refresh) e o
 * `access_token_expire_in` vem como EPOCH UNIX. Por isso:
 *   - refresh() e' idempotente (guard isExpired) — o HttpClientFactory chama
 *     antes de TODA request; sem guard rotacionaria o token a cada chamada.
 *   - Cache lock serializa refresh concorrente (senao 2 workers rotacionam
 *     um o token do outro → invalida).
 *   - forceRefresh() ignora o guard de expiry (usado quando o TikTok ja'
 *     rejeitou o token no servidor: 105002/105004/105005), mas reaproveita
 *     se OUTRO worker ja' rotacionou (token na DB != rejeitado) — evita
 *     trocar 2x desnecessariamente.
 *
 * NB: desativacao de integration em erro logico (refresh_token revogado) NAO
 * acontece aqui — e' responsabilidade do host (catch da excecao), consistente
 * com os outros conectores da lib.
 */
class TokenRefresher
{
    private const AUTH_HOST = 'https://auth.tiktok-shops.com';

    private const REFRESH_PATH = '/api/v2/token/refresh';

    public static function refresh(MarketplaceIntegration $integration): string
    {
        $token = $integration->getAccessToken();

        if ($token && ! self::isExpired($integration)) {
            return $token;
        }

        return self::doRefresh($integration, force: false);
    }

    /** Forca refresh ignorando o guard de expiry (105002 na borda / sweep). */
    public static function forceRefresh(MarketplaceIntegration $integration): string
    {
        return self::doRefresh($integration, force: true, rejectedToken: $integration->getAccessToken());
    }

    private static function doRefresh(MarketplaceIntegration $integration, bool $force, ?string $rejectedToken = null): string
    {
        $lock = Cache::lock('tiktok_token_refresh_'.$integration->getIntegrationIdentifier(), 15);

        try {
            $lock->block(10);

            // Recarrega do storage — outra thread pode ter rotacionado.
            if (method_exists($integration, 'refresh')) {
                $integration->refresh();
            }

            // Reaproveita se outra thread ja' renovou enquanto esperavamos.
            // Normal: confia no expiry. Force: so' reaproveita se o token
            // mudou em relacao ao que o TikTok rejeitou.
            if ($force) {
                if ($integration->getAccessToken() !== $rejectedToken && ! self::isExpired($integration)) {
                    return $integration->getAccessToken();
                }
            } elseif ($integration->getAccessToken() && ! self::isExpired($integration)) {
                return $integration->getAccessToken();
            }

            $settings = $integration->getMarketplaceSettings();
            $appKey = (string) ($settings['app_key'] ?? '');
            $appSecret = (string) ($settings['app_secret'] ?? '');

            if (! $appKey || ! $appSecret) {
                throw new TiktokAuthenticationException('Integration TikTok sem app_key/app_secret.');
            }

            $response = Http::asJson()->timeout(15)->connectTimeout(10)->get(self::AUTH_HOST.self::REFRESH_PATH, [
                'app_key' => $appKey,
                'app_secret' => $appSecret,
                'refresh_token' => $integration->getRefreshToken(),
                'grant_type' => 'refresh_token',
            ]);

            $data = $response->json() ?? [];
            $tiktokCode = $data['code'] ?? null;

            if ($response->failed() || $tiktokCode !== 0) {
                Log::error('TikTok token refresh failed', [
                    'status' => $response->status(),
                    'tiktok_code' => $tiktokCode,
                    'integration_id' => $integration->getIntegrationIdentifier(),
                ]);
                throw new TiktokAuthenticationException(sprintf(
                    'Falha ao renovar token TikTok (code=%s, HTTP %d).',
                    $tiktokCode ?? 'unknown',
                    $response->status(),
                ));
            }

            $payload = $data['data'] ?? [];

            if (empty($payload['access_token']) || empty($payload['refresh_token'])) {
                throw new TiktokAuthenticationException('Resposta TikTok sem access_token/refresh_token.');
            }

            // access_token_expire_in e' EPOCH UNIX → converte pra TTL em segundos.
            // updateTokens faz now()->addSeconds(ttl) no locale do app (round-trip
            // consistente, resolve o shift de TZ que causava 105002).
            $integration->updateTokens(
                $payload['access_token'],
                $payload['refresh_token'],
                isset($payload['access_token_expire_in'])
                    ? max(60, (int) $payload['access_token_expire_in'] - time())
                    : 86400,
            );

            return $payload['access_token'];
        } finally {
            optional($lock)->release();
        }
    }

    private static function isExpired(MarketplaceIntegration $integration): bool
    {
        if (method_exists($integration, 'isExpired')) {
            return $integration->isExpired();
        }

        $settings = $integration->getMarketplaceSettings();
        $expiresAt = $settings['expires_at'] ?? null;
        if (! $expiresAt) {
            return false;
        }

        return (new \DateTime($expiresAt))->getTimestamp() < (time() + 300);
    }
}
