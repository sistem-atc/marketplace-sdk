<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Application Management API 2023-11-30 — rotação do client secret da
 * aplicação SP-API.
 *
 * GRANTLESS: usa token client_credentials com scope
 * `sellingpartnerapi::client_credential:rotation` (não depende de seller
 * autorizado). O secret novo NÃO vem na resposta: a Amazon envia pra fila SQS
 * registrada no Developer Console (notificação APPLICATION_OAUTH_CLIENT_NEW_SECRET);
 * o antigo continua válido por 7 dias.
 */
class ApplicationManagement
{
    /** Scope LWA da operação grantless de rotação de secret. */
    public const GRANTLESS_SCOPE = 'sellingpartnerapi::client_credential:rotation';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Rotaciona o client secret da aplicação (POST /applications/2023-11-30/clientSecret).
     * Sem body; resposta 204 vazia (o secret chega via SQS). Rate limit:
     * 0.0167 req/s (1 a cada ~60s) + burst 1.
     *
     * @return array<string, mixed>
     */
    public function rotateApplicationClientSecret(): array
    {
        return $this->client->postGrantless(
            '/applications/2023-11-30/clientSecret',
            self::GRANTLESS_SCOPE,
        );
    }
}
