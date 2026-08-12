<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Ads\Support;

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Tiktok\Ads\Exceptions\TiktokAdsRequestException;

/**
 * OAuth da MARKETING API do TikTok for Business (business-api.tiktok.com) —
 * NÃO confundir com o OAuth do TikTok Shop (Tiktok\Support\OAuth, outro
 * portal e outro host).
 *
 * Fluxo (portal de apps do TikTok for Business Developers):
 *   1. authorizationUrl() -> operador autoriza no portal e volta pro
 *      redirect_uri cadastrado no app com ?auth_code=...
 *   2. exchangeAuthCode() -> troca o auth_code por access_token de LONGA
 *      DURAÇÃO (sem refresh token — não expira até ser revogado) + a lista
 *      de advertiser_ids autorizados.
 *
 * O host (app consumidor) monta redirect/callback e persiste o token.
 */
class OAuth
{
    private const AUTHORIZE_URL = 'https://business-api.tiktok.com/portal/auth';

    private const TOKEN_PATH = '/open_api/v1.3/oauth2/access_token/';

    public static function authorizationUrl(string $appId, string $redirectUri, ?string $state = null): string
    {
        $params = ['app_id' => $appId, 'redirect_uri' => $redirectUri];

        if ($state !== null && $state !== '') {
            $params['state'] = $state;
        }

        return self::AUTHORIZE_URL.'?'.http_build_query($params);
    }

    /**
     * Troca o auth_code do callback pelo token de longa duração.
     *
     * @return array{access_token: string, advertiser_ids: array<int, string>, scope: array<int, int>}
     *
     * @throws TiktokAdsRequestException
     */
    public static function exchangeAuthCode(string $appId, string $secret, string $authCode): array
    {
        $baseUrl = (string) config('marketplaces.tiktok_ads.base_url', 'https://business-api.tiktok.com');

        $response = Http::acceptJson()->post($baseUrl.self::TOKEN_PATH, [
            'app_id' => $appId,
            'secret' => $secret,
            'auth_code' => $authCode,
        ])->throw()->json();

        if (($response['code'] ?? -1) !== 0) {
            throw new TiktokAdsRequestException(
                (string) ($response['message'] ?? 'erro desconhecido no oauth2/access_token'),
                (int) ($response['code'] ?? 0),
            );
        }

        return $response['data'];
    }
}
