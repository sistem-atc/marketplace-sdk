<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Support;

use SistemAtc\Marketplaces\ConectaLa\Exceptions\ConectaLaAuthenticationException;
use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Cliente HTTP da Conecta Lá. Auth é por HEADER (não OAuth): as chaves ficam nas
 * settings da Integration e viram os headers `x-*` que a API exige. Um seller só
 * pode ter UMA integração por loja (regra da plataforma).
 *
 * Settings esperadas (as que a API usa; nem todo endpoint exige todas):
 *   api_key         -> x-api-key         (obrigatória)
 *   store_key       -> x-store-key
 *   provider_key    -> x-provider-key
 *   store_seller_key-> x-store-seller-key
 *   user_email      -> x-user-email
 *   email           -> x-email
 */
class HttpClientFactory
{
    /** settings key => header name. */
    private const HEADER_MAP = [
        'api_key' => 'x-api-key',
        'store_key' => 'x-store-key',
        'provider_key' => 'x-provider-key',
        'store_seller_key' => 'x-store-seller-key',
        'user_email' => 'x-user-email',
        'email' => 'x-email',
    ];

    public static function make(MarketplaceIntegration $integration): PendingRequest
    {
        if (! $integration->isIntegrationActive()) {
            throw new ConectaLaAuthenticationException('Integração Conecta Lá inativa.');
        }

        $settings = $integration->getMarketplaceSettings();

        if (empty($settings['api_key'])) {
            throw new ConectaLaAuthenticationException('api_key ausente nas configurações da Conecta Lá.');
        }

        $headers = [];
        foreach (self::HEADER_MAP as $key => $header) {
            if (! empty($settings[$key])) {
                $headers[$header] = (string) $settings[$key];
            }
        }

        return Http::baseUrl((string) config('marketplaces.conectala.api_base', 'http://teste.conectala.com.br/app/Api/V1'))
            ->withHeaders($headers)
            ->timeout(180)
            ->connectTimeout(10)
            ->acceptJson();
    }
}
