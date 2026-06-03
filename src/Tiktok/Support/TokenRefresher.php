<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Tiktok\Exceptions\TiktokAuthenticationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokenRefresher
{
    private const AUTH_HOST = 'https://auth.tiktok-shops.com';
    private const REFRESH_PATH = '/api/v2/token/refresh';

    public static function refresh(MarketplaceIntegration $integration): string
    {
        return self::doRefresh($integration);
    }

    public static function forceRefresh(MarketplaceIntegration $integration): string
    {
        return self::doRefresh($integration);
    }

    private static function doRefresh(MarketplaceIntegration $integration): string
    {
        $settings = $integration->getMarketplaceSettings();
        $appKey = $settings['app_key'] ?? '';
        $appSecret = $settings['app_secret'] ?? '';

        if (!$appKey || !$appSecret) {
            throw new TiktokAuthenticationException('TikTok missing app_key/app_secret.');
        }

        $response = Http::asJson()->get(self::AUTH_HOST . self::REFRESH_PATH, [
            'app_key' => $appKey,
            'app_secret' => $appSecret,
            'refresh_token' => $integration->getRefreshToken(),
            'grant_type' => 'refresh_token',
        ]);

        $data = $response->json() ?? [];
        if ($response->failed() || ($data['code'] ?? 0) !== 0) {
            Log::error('TikTok token refresh failed', [
                'status' => $response->status(),
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);
            throw new TiktokAuthenticationException('Falha ao renovar token TikTok.');
        }

        $payload = $data['data'] ?? [];
        $integration->updateTokens(
            $payload['access_token'],
            $payload['refresh_token'],
            // TikTok retorna epoch unix em access_token_expire_in, mas updateTokens espera TTL em segundos.
            // Aqui simplificamos ou adaptamos conforme a necessidade.
            isset($payload['access_token_expire_in']) ? (int)($payload['access_token_expire_in'] - time()) : 86400
        );

        return $payload['access_token'];
    }
}
