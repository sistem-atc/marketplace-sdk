<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeAuthenticationException;

/**
 * Fluxo OAuth (Authorization Code) do Shopee Open Platform.
 *
 * O host (Bunker) so' fornece partner_id/partner_key do app (Console em
 * open.shopee.com). Esta classe cuida da mecanica pura:
 *   1. authorizationUrl()          -> URL /api/v2/shop/auth_partner assinada
 *                                     pra onde redirecionar o operador
 *   2. exchangeAuthorizationCode() -> troca o `code` do callback por tokens
 *
 * Diferencas vs Mercado Livre:
 *   - Nao ha PKCE.
 *   - A Shopee NAO ecoa `state` no callback (o auth_partner so' devolve
 *     ?code=&shop_id=). A amarracao callback->integration fica na SESSAO do
 *     host, nao na URL.
 *   - As chamadas sao assinadas via HMAC-SHA256 na query string
 *     (SignatureGenerator::publicSign), nao header Bearer.
 *
 * O redirect/callback HTTP + persistencia ficam no controller do host. O
 * refresh (access_token 4h, refresh_token 30d rolling) continua em
 * TokenRefresher::refresh().
 */
class OAuth
{
    /** Path assinado pra construir a URL de autorizacao do vendedor. */
    private const AUTH_PATH = '/api/v2/shop/auth_partner';

    /** Path do grant inicial (code -> tokens). Refresh usa outro path. */
    private const TOKEN_PATH = '/api/v2/auth/token/get';

    /**
     * URL de autorizacao pra onde o operador e' redirecionado. Ao autorizar o
     * app na loja Shopee, a Shopee redireciona de volta pro redirect com
     * ?code=...&shop_id=...
     */
    public static function authorizationUrl(
        int $partnerId,
        string $partnerKey,
        string $redirectUri,
        ?int $timestamp = null,
    ): string {
        $timestamp ??= time();
        $sign = SignatureGenerator::publicSign($partnerId, self::AUTH_PATH, $timestamp, $partnerKey);

        return self::baseUrl().self::AUTH_PATH.'?'.http_build_query([
            'partner_id' => $partnerId,
            'timestamp'  => $timestamp,
            'sign'       => $sign,
            'redirect'   => $redirectUri,
        ]);
    }

    /**
     * Troca o authorization_code (do callback) pelo par access_token +
     * refresh_token inicial. NAO persiste — o host decide como salvar.
     *
     * @return array{access_token: string, refresh_token: string, expires_in: int}
     */
    public static function exchangeAuthorizationCode(
        int $partnerId,
        string $partnerKey,
        string $code,
        int $shopId,
        ?int $timestamp = null,
    ): array {
        $timestamp ??= time();
        $sign = SignatureGenerator::publicSign($partnerId, self::TOKEN_PATH, $timestamp, $partnerKey);

        $url = self::baseUrl().self::TOKEN_PATH."?partner_id={$partnerId}&timestamp={$timestamp}&sign={$sign}";

        $response = Http::asJson()->timeout(15)->post($url, [
            'code'       => $code,
            'shop_id'    => $shopId,
            'partner_id' => $partnerId,
        ]);

        $data = $response->json() ?? [];

        // Shopee devolve HTTP 200 com `error` preenchido em falha logica.
        if ($response->failed() || ! empty($data['error'])) {
            Log::error('Shopee authorization_code exchange failed', [
                'status'  => $response->status(),
                'error'   => $data['error'] ?? null,
                'message' => $data['message'] ?? null,
                'shop_id' => $shopId,
            ]);

            throw new ShopeeAuthenticationException(
                'Falha na troca do code Shopee: '
                .($data['message'] ?? $data['error'] ?? 'HTTP '.$response->status())
            );
        }

        if (empty($data['refresh_token']) || empty($data['access_token'])) {
            throw new ShopeeAuthenticationException(
                'Resposta do Shopee sem access_token/refresh_token.'
            );
        }

        return [
            'access_token'  => (string) $data['access_token'],
            'refresh_token' => (string) $data['refresh_token'],
            'expires_in'    => (int) ($data['expire_in'] ?? 14400),
        ];
    }

    private static function baseUrl(): string
    {
        return rtrim(config('marketplaces.shopee.base_url', 'https://partner.shopeemobile.com'), '/');
    }
}
