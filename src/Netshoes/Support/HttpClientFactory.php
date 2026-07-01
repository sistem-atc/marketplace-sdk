<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Netshoes\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Netshoes\Exceptions\NetshoesAuthenticationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Cliente HTTP base Netshoes (Grupo Netshoes — gateway Sensedia).
 *
 * Auth = PAR DE HEADERS ESTATICOS em TODA request (CONFIRMADO por testes
 * reais — NAO ha OAuth/exchange; /oauth/* devolve 404):
 *   - `client_id`     = settings['client_id']  (header literal `client_id`)
 *   - `access_token`  = getAccessToken() (coluna access_token; header literal
 *                       `access_token`). Fornecido pela Netshoes (analista),
 *                       NAO e' o client_secret.
 *   - `content-type: application/json`
 *
 * Nenhum dos dois expira via exchange — sem TokenRefresher.
 *
 * Base URL configuravel prod/homolog:
 *   - PROD    https://api-marketplace.netshoes.com.br  (HTTPS)
 *   - HOMOLOG http://api-sandbox.netshoes.com.br       (HTTP — https reseta)
 * Resolucao: settings['environment']='sandbox' OU settings['base_url'] explicito
 * tem prioridade sobre o default de config('marketplaces.netshoes.api_base').
 */
class HttpClientFactory
{
    public const PROD_BASE = 'https://api-marketplace.netshoes.com.br';

    public const SANDBOX_BASE = 'http://api-sandbox.netshoes.com.br';

    public static function make(MarketplaceIntegration $integration): PendingRequest
    {
        if (! $integration->isIntegrationActive()) {
            throw new NetshoesAuthenticationException('Integração Netshoes inativa.');
        }

        $settings = $integration->getMarketplaceSettings();

        $clientId = (string) ($settings['client_id'] ?? '');
        $accessToken = (string) ($integration->getAccessToken() ?? ($settings['access_token'] ?? ''));

        if ($clientId === '' || $accessToken === '') {
            throw new NetshoesAuthenticationException(
                'Credenciais Netshoes ausentes (client_id no settings + access_token).'
            );
        }

        $baseUrl = self::resolveBaseUrl($settings);

        return Http::baseUrl(rtrim($baseUrl, '/'))
            ->withHeaders([
                'client_id' => $clientId,
                'access_token' => $accessToken,
                'content-type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'SistemAtcMarketplaces/1.0 (Netshoes)',
            ])
            ->timeout(60)
            ->connectTimeout(10);
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    private static function resolveBaseUrl(array $settings): string
    {
        // Override explicito sempre vence.
        if (! empty($settings['base_url'])) {
            return (string) $settings['base_url'];
        }

        $env = strtolower((string) ($settings['environment'] ?? 'production'));
        if (in_array($env, ['sandbox', 'homolog', 'homologation', 'staging'], true)) {
            return (string) config('marketplaces.netshoes.sandbox_base', self::SANDBOX_BASE);
        }

        return (string) config('marketplaces.netshoes.api_base', self::PROD_BASE);
    }
}
