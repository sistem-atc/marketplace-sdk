<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeAuthenticationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Refresh do access_token Shopee.
 *
 * access_token TTL 14400s (4h); refresh_token e' ROLLING (Shopee gera um
 * novo a cada refresh). Por isso 2 cuidados:
 *   1. Idempotencia — o HttpClientFactory chama refresh() antes de TODA
 *      request; sem guard faria um POST a cada chamada e rotacionaria o
 *      refresh_token toda vez.
 *   2. Lock — refresh concorrente rotacionaria o refresh_token um do outro,
 *      invalidando-os. O lock serializa por integration.
 */
class TokenRefresher
{
    private const TOKEN_PATH = '/api/v2/auth/access_token/get';

    public static function refresh(MarketplaceIntegration $integration): string
    {
        $token = $integration->getAccessToken();

        if ($token && ! self::isExpired($integration)) {
            return $token;
        }

        $lock = Cache::lock('shopee_token_refresh_'.$integration->getIntegrationIdentifier(), 15);

        try {
            $lock->block(10);

            // Double-check apos o lock — outra request pode ter renovado.
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
        $settings = $integration->getMarketplaceSettings();
        $partnerId = (int) ($settings['partner_id'] ?? 0);
        $partnerKey = (string) ($settings['partner_key'] ?? '');
        $shopId = (int) ($settings['shop_id'] ?? 0);

        if (! $partnerId || ! $partnerKey || ! $shopId) {
            throw new ShopeeAuthenticationException('Integration Shopee sem credenciais.');
        }

        $timestamp = time();
        $sign = SignatureGenerator::publicSign($partnerId, self::TOKEN_PATH, $timestamp, $partnerKey);

        $baseUrl = rtrim(config('marketplaces.shopee.base_url', 'https://partner.shopeemobile.com'), '/');
        $url = "{$baseUrl}".self::TOKEN_PATH."?partner_id={$partnerId}&timestamp={$timestamp}&sign={$sign}";

        $response = Http::asJson()->post($url, [
            'partner_id' => $partnerId,
            'shop_id' => $shopId,
            'refresh_token' => $integration->getRefreshToken(),
        ]);

        $data = $response->json() ?? [];
        if ($response->failed() || ! empty($data['error'])) {
            Log::error('Shopee token refresh failed', [
                'status' => $response->status(),
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);
            throw new ShopeeAuthenticationException('Falha ao renovar token Shopee.');
        }

        $integration->updateTokens(
            $data['access_token'],
            $data['refresh_token'],
            $data['expire_in'] ?? 14400
        );

        return $data['access_token'];
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
