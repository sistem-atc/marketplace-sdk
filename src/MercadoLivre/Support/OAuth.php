<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreAuthenticationException;

/**
 * Fluxo OAuth (Authorization Code + PKCE) do Mercado Livre.
 *
 * O host (Bunker) so' fornece client_id/client_secret do app (gerados no
 * DevCenter developers.mercadolivre.com). Esta classe cuida da mecanica pura:
 *   1. authorizationUrl()          -> URL pra onde redirecionar o operador
 *   2. exchangeAuthorizationCode() -> troca o `code` do callback por tokens
 *
 * O redirect/callback HTTP + persistencia ficam no controller do host (o SDK e'
 * agnostico de framework). O refresh continua em TokenRefresher::refresh().
 *
 * PKCE (S256): generatePkce() devolve o par verifier/challenge. O challenge vai
 * na authorizationUrl; o verifier o host guarda na sessao e devolve no exchange.
 */
class OAuth
{
    /** Dominio de autorizacao (por pais — BR). Token endpoint e' global. */
    private const AUTH_URL  = 'https://auth.mercadolivre.com.br/authorization';
    private const TOKEN_URL = 'https://api.mercadolibre.com/oauth/token';

    /**
     * Gera o par PKCE (code_verifier + code_challenge S256), ambos base64url.
     *
     * @return array{verifier: string, challenge: string}
     */
    public static function generatePkce(): array
    {
        $verifier  = self::base64Url(random_bytes(64));
        $challenge = self::base64Url(hash('sha256', $verifier, true));

        return ['verifier' => $verifier, 'challenge' => $challenge];
    }

    /**
     * URL de autorizacao pra onde o operador e' redirecionado. Ao autorizar o
     * app na conta ML, o ML redireciona de volta pro redirect_uri com ?code=...
     */
    public static function authorizationUrl(
        string $clientId,
        string $redirectUri,
        string $state,
        ?string $codeChallenge = null,
    ): string {
        $params = [
            'response_type' => 'code',
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'state'         => $state,
        ];

        if ($codeChallenge !== null) {
            $params['code_challenge']        = $codeChallenge;
            $params['code_challenge_method'] = 'S256';
        }

        return self::AUTH_URL.'?'.http_build_query($params);
    }

    /**
     * Troca o authorization_code (do callback) pelo par access_token +
     * refresh_token inicial. NAO persiste — o host decide como salvar.
     *
     * @return array{access_token: string, refresh_token: string, expires_in: int}
     */
    public static function exchangeAuthorizationCode(
        string $clientId,
        string $clientSecret,
        string $code,
        string $redirectUri,
        ?string $codeVerifier = null,
    ): array {
        $payload = [
            'grant_type'    => 'authorization_code',
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'code'          => $code,
            'redirect_uri'  => $redirectUri,
        ];

        if ($codeVerifier !== null) {
            $payload['code_verifier'] = $codeVerifier;
        }

        $response = Http::asForm()->timeout(15)->post(self::TOKEN_URL, $payload);

        if ($response->failed()) {
            Log::error('Mercado Livre authorization_code exchange failed', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            throw new MercadoLivreAuthenticationException(
                'Falha na troca do code Mercado Livre: HTTP '.$response->status()
                .' — '.($response->json('message') ?? $response->json('error') ?? 'desconhecido')
            );
        }

        $data = $response->json();

        // Sem refresh_token = o app nao tem o scope offline_access habilitado.
        if (empty($data['refresh_token'])) {
            throw new MercadoLivreAuthenticationException(
                'Resposta do Mercado Livre sem refresh_token — habilite o scope offline_access no app.'
            );
        }

        return [
            'access_token'  => (string) $data['access_token'],
            'refresh_token' => (string) $data['refresh_token'],
            'expires_in'    => (int) ($data['expires_in'] ?? 21600),
        ];
    }

    private static function base64Url(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
