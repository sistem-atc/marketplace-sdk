<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\MercadoPago\Exceptions\MercadoPagoAuthenticationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Refresh do access_token Mercado Pago.
 *
 * MP: tokens expiram em ~6h. Refresh via:
 *   POST https://api.mercadopago.com/oauth/token
 *   Content-Type: application/x-www-form-urlencoded
 *
 *   grant_type=refresh_token
 *   client_id={app_id}
 *   client_secret={secret}
 *   refresh_token={refresh}
 *
 * Resposta inclui novo access_token + novo refresh_token (rolling) +
 * expires_in (segundos). Por causa do refresh_token rolling, 2 cuidados
 * sao obrigatorios:
 *
 *   1. Idempotencia (skip se token ainda valido) — o HttpClientFactory chama
 *      refresh() ANTES de TODA chamada de API. Sem o guard, cada request
 *      faria um POST no endpoint de token e rotacionaria o refresh_token.
 *   2. Lock — duas chamadas concorrentes que passem o guard ao mesmo tempo
 *      correriam pra rotacionar, invalidando o refresh_token uma da outra.
 *      O lock serializa o refresh por integration.
 *
 * NOTA: ao contrario do conector interno do host, a lib NAO desativa a
 * integration em caso de falha — isso fica como responsabilidade do host
 * (consistente com os demais TokenRefreshers da lib).
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/docs/security/oauth/creation
 */
class TokenRefresher
{
    /** Margem de seguranca (segundos) antes do expires_at pra considerar expirado. */
    private const EXPIRY_MARGIN_SECONDS = 300;

    public static function refresh(MarketplaceIntegration $integration): string
    {
        $token = $integration->getAccessToken();

        // Skipa refresh se o token ainda e' valido (com margem). Evita chamada
        // desnecessaria ao endpoint de token a cada request.
        if ($token && ! self::isExpired($integration)) {
            return $token;
        }

        $lock = Cache::lock('mp_token_refresh_'.$integration->getIntegrationIdentifier(), 15);

        try {
            $lock->block(10);

            // Reload do model (Eloquent no host) — outro worker pode ter
            // rotacionado o token enquanto esperavamos o lock.
            if (method_exists($integration, 'refresh')) {
                $integration->refresh();
            }

            // Double-check apos adquirir o lock.
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
        // Credenciais sao SEMPRE per-Integration (settings). Cada company
        // cadastra seu app MP proprio — sem fallback global.
        $settings     = $integration->getMarketplaceSettings();
        $clientId     = $settings['client_id'] ?? null;
        $clientSecret = $settings['client_secret'] ?? null;
        $tokenUrl     = config('marketplaces.mercadopago.oauth_token_url', 'https://api.mercadopago.com/oauth/token');

        if (! $clientId || ! $clientSecret) {
            throw new MercadoPagoAuthenticationException(
                'Integration MP sem credenciais. Preencha client_id e client_secret nos settings.'
            );
        }

        $response = Http::asForm()->post($tokenUrl, [
            'grant_type'    => 'refresh_token',
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $integration->getRefreshToken(),
        ]);

        if ($response->failed()) {
            Log::error('Mercado Pago token refresh failed', [
                'status'         => $response->status(),
                'integration_id' => $integration->getIntegrationIdentifier(),
            ]);

            if ($response->status() === 429 || $response->status() >= 500) {
                throw new MercadoPagoAuthenticationException(sprintf(
                    'Mercado Pago indisponivel para refresh do token (HTTP %d). Tentar novamente.',
                    $response->status(),
                ));
            }

            // 4xx terminal — refresh_token revogado/expirado.
            throw new MercadoPagoAuthenticationException(
                'Nao foi possivel renovar o token do Mercado Pago: '.$response->body()
            );
        }

        $data = $response->json();

        if (empty($data['access_token'])) {
            throw new MercadoPagoAuthenticationException('Resposta Mercado Pago sem access_token.');
        }

        $integration->updateTokens(
            $data['access_token'],
            $data['refresh_token'] ?? $integration->getRefreshToken(),
            $data['expires_in'] ?? 21600,
        );

        Log::info('Mercado Pago token refresh OK', [
            'integration_id' => $integration->getIntegrationIdentifier(),
            'expires_in'     => $data['expires_in'] ?? null,
        ]);

        return $data['access_token'];
    }

    private static function isExpired(MarketplaceIntegration $integration): bool
    {
        // O host implementa Integration::isExpired() (com margem propria).
        if (method_exists($integration, 'isExpired')) {
            return $integration->isExpired();
        }

        $settings  = $integration->getMarketplaceSettings();
        $expiresAt = $settings['expires_at'] ?? null;
        if (! $expiresAt) {
            return false; // Sem data → assume valido.
        }

        return (new \DateTime($expiresAt))->getTimestamp()
            < (time() + self::EXPIRY_MARGIN_SECONDS);
    }
}
