<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\MercadoPago\Exceptions\MercadoPagoAuthenticationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Cria PendingRequest pre-configurado pro Mercado Pago.
 *
 * Auth MP: OAuth2 Bearer (igual ML).
 *   Authorization: Bearer APP_USR-{access_token}
 *
 * Refresh automatico via TokenRefresher quando o token estiver expirado.
 */
class HttpClientFactory
{
    public static function make(MarketplaceIntegration $integration): PendingRequest
    {
        if (! $integration->isIntegrationActive()) {
            throw new MercadoPagoAuthenticationException(sprintf(
                'Integracao Mercado Pago #%s esta desativada. '.
                'Reconecte via OAuth no painel developers.mercadopago.com.br.',
                $integration->getIntegrationIdentifier(),
            ));
        }

        TokenRefresher::refresh($integration);

        $base = rtrim((string) config('marketplaces.mercadopago.api_base', 'https://api.mercadopago.com'), '/');

        return Http::baseUrl($base)
            ->withToken($integration->getAccessToken())
            ->timeout((int) config('marketplaces.mercadopago.timeout', 60))
            ->connectTimeout((int) config('marketplaces.mercadopago.connect_timeout', 10))
            ->acceptJson();
    }
}
