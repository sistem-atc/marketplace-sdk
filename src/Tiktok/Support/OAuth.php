<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SistemAtc\Marketplaces\Tiktok\Exceptions\TiktokAuthenticationException;

/**
 * Fluxo OAuth (Authorization Code) do TikTok Shop.
 *
 * Diferente do refresh (TokenRefresher), aqui e' a obtencao INICIAL:
 *   1. authorizationUrl()          -> URL pra onde o seller e' redirecionado
 *      (services.tiktokshop.com/open/authorize?service_id=...). Ao autorizar,
 *      o TikTok volta pro redirect_uri cadastrado com ?app_key=...&code=...
 *   2. exchangeAuthorizationCode() -> troca o `code` (auth_code) por
 *      access_token + refresh_token (Auth API auth.tiktok-shops.com,
 *      grant_type=authorized_code).
 *
 * O host (app) e' quem monta redirect/callback + persiste. O `shop_cipher`
 * NAO vem aqui — e' obtido depois via a Authorization API
 * (/authorization/202309/shops) com o access_token ja' emitido.
 *
 * `access_token_expire_in` vem como EPOCH UNIX (idem refresh) — quem persiste
 * converte pra TTL relativo (evita o shift de TZ que causava 105002).
 */
class OAuth
{
    private const AUTHORIZE_URL = 'https://services.tiktokshop.com/open/authorize';

    private const AUTH_HOST = 'https://auth.tiktok-shops.com';

    private const TOKEN_GET_PATH = '/api/v2/token/get';

    /** URL de autorizacao pra onde o seller e' redirecionado. */
    public static function authorizationUrl(string $serviceId, ?string $state = null): string
    {
        $params = ['service_id' => $serviceId];

        if ($state !== null && $state !== '') {
            $params['state'] = $state;
        }

        return self::AUTHORIZE_URL.'?'.http_build_query($params);
    }

    /**
     * Troca o auth_code (do callback) por access_token + refresh_token.
     * NAO persiste — o host decide como salvar. Retorna o `data` cru do TikTok
     * (inclui access_token, refresh_token, access_token_expire_in EPOCH,
     * refresh_token_expire_in, open_id, seller_name, granted_scopes...).
     *
     * @return array<string, mixed>
     */
    public static function exchangeAuthorizationCode(string $appKey, string $appSecret, string $authCode): array
    {
        $response = Http::asJson()->timeout(15)->connectTimeout(10)->get(self::AUTH_HOST.self::TOKEN_GET_PATH, [
            'app_key'    => $appKey,
            'app_secret' => $appSecret,
            'auth_code'  => $authCode,
            'grant_type' => 'authorized_code',
        ]);

        $data = $response->json() ?? [];
        $code = $data['code'] ?? null;

        // TikTok retorna HTTP 200 com code!=0 em erro logico — checar os dois.
        if ($response->failed() || $code !== 0) {
            Log::error('TikTok authorized_code exchange failed', [
                'status'      => $response->status(),
                'tiktok_code' => $code,
                'message'     => $data['message'] ?? null,
            ]);

            throw new TiktokAuthenticationException(sprintf(
                'Falha na troca do auth_code TikTok (code=%s, HTTP %d): %s',
                $code ?? 'unknown',
                $response->status(),
                $data['message'] ?? 'desconhecido',
            ));
        }

        $payload = $data['data'] ?? [];

        if (empty($payload['access_token']) || empty($payload['refresh_token'])) {
            throw new TiktokAuthenticationException('Resposta TikTok (token/get) sem access_token/refresh_token.');
        }

        return $payload;
    }
}
