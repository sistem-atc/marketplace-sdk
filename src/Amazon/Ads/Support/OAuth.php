<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Support;

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;

/**
 * OAuth (Login with Amazon) da AMAZON ADS API — Security Profile PRÓPRIO,
 * separado do da SP-API. Escopo: `advertising::campaign_management`.
 *
 * Fluxo clássico LwA: authorizationUrl() manda o operador pro consent →
 * callback recebe ?code=... → exchangeAuthorizationCode() troca por
 * access_token (1h) + refresh_token (longa duração). refreshAccessToken()
 * renova o access; o refresh_token NÃO rotaciona.
 *
 * Credenciais (client_id/secret) vêm do HOST por parâmetro — o SDK não lê
 * config de app consumidor.
 */
class OAuth
{
    /** Consent na Amazon BR (o login do operador é .com.br). */
    private const AUTHORIZE_URL = 'https://www.amazon.com.br/ap/oa';

    private const TOKEN_URL = 'https://api.amazon.com/auth/o2/token';

    public const SCOPE = 'advertising::campaign_management';

    public static function authorizationUrl(string $clientId, string $redirectUri, ?string $state = null): string
    {
        $params = [
            'client_id' => $clientId,
            'scope' => self::SCOPE,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
        ];

        if ($state !== null && $state !== '') {
            $params['state'] = $state;
        }

        return self::AUTHORIZE_URL.'?'.http_build_query($params);
    }

    /**
     * @return array{access_token: string, refresh_token: string, expires_in: int}
     *
     * @throws AmazonAdsRequestException
     */
    public static function exchangeAuthorizationCode(string $clientId, string $clientSecret, string $code, string $redirectUri): array
    {
        return self::token([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);
    }

    /**
     * @return array{access_token: string, refresh_token?: string, expires_in: int}
     *
     * @throws AmazonAdsRequestException
     */
    public static function refreshAccessToken(string $clientId, string $clientSecret, string $refreshToken): array
    {
        return self::token([
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);
    }

    /** @throws AmazonAdsRequestException */
    private static function token(array $params): array
    {
        $response = Http::asForm()->post(self::TOKEN_URL, $params);
        $data = $response->json() ?? [];

        if (! $response->successful() || empty($data['access_token'])) {
            throw new AmazonAdsRequestException(
                (string) ($data['error_description'] ?? $data['error'] ?? 'resposta sem access_token do LwA'),
                $response->status(),
            );
        }

        return $data;
    }
}
