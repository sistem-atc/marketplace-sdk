<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Support;

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoPago\Exceptions\MercadoPagoAuthenticationException;

/**
 * OAuth do Mercado Pago (Authorization Code) — espelha `OAuthClient` do SDK
 * oficial. Sem integration: roda ANTES de existir token, no fluxo em que a
 * company conecta a conta MP pelo painel.
 *
 *   1. authorizationUrl()  → redireciona o usuario pro MP autorizar
 *   2. exchangeAuthorizationCode() → troca o `code` do callback por tokens
 *   3. refresh() → renova (o TokenRefresher faz isso sozinho no dia a dia;
 *      exposto aqui por paridade com o SDK)
 *
 * O token de acesso de vendedor dura ~6h e o refresh_token e' ROLLING: a
 * resposta traz um novo a cada refresh e o anterior morre.
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/docs/security/oauth/creation
 */
class OAuth
{
    private const AUTH_URL = 'https://auth.mercadopago.com.br/authorization';

    public static function authorizationUrl(string $clientId, string $redirectUri, string $state): string
    {
        return self::AUTH_URL.'?'.http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'platform_id' => 'mp',
            'state' => $state,
            'redirect_uri' => $redirectUri,
        ]);
    }

    /**
     * @return array{access_token: string, refresh_token?: string, expires_in?: int, user_id?: int, scope?: string, public_key?: string, live_mode?: bool}
     */
    public static function exchangeAuthorizationCode(
        string $clientId,
        string $clientSecret,
        string $code,
        string $redirectUri,
    ): array {
        return self::token([
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'redirect_uri' => $redirectUri,
        ]);
    }

    /**
     * @return array{access_token: string, refresh_token?: string, expires_in?: int}
     */
    public static function refresh(string $clientId, string $clientSecret, string $refreshToken): array
    {
        return self::token([
            'grant_type' => 'refresh_token',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken,
        ]);
    }

    /**
     * @param  array<string, string>  $payload
     * @return array<string, mixed>
     */
    private static function token(array $payload): array
    {
        $url = (string) config('marketplaces.mercadopago.oauth_token_url', 'https://api.mercadopago.com/oauth/token');
        $response = Http::asForm()->timeout(15)->post($url, $payload);

        if ($response->failed()) {
            throw new MercadoPagoAuthenticationException(sprintf(
                'OAuth Mercado Pago falhou (HTTP %d): %s',
                $response->status(),
                $response->body(),
            ));
        }

        $data = $response->json() ?? [];

        if (empty($data['access_token'])) {
            throw new MercadoPagoAuthenticationException('Resposta OAuth Mercado Pago sem access_token.');
        }

        return $data;
    }
}
