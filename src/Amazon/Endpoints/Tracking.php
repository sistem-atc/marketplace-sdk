<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Tracking API 2026-01-30 (path /tracking/2026-01-30) — rastreio unificado
 * de remessas por vários identificadores (id Amazon, ACSIN, AFTN, container,
 * HBL ou tracking da transportadora).
 *
 * O modelo não publica rate limit. O header opcional `Accept-Language`
 * (idioma das descrições de evento) vai via `$acceptLanguage`; só é enviado
 * quando informado.
 *
 * Resposta no nível raiz (sem `payload`).
 */
class Tracking
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Rastreio de uma remessa (GET /tracking/2026-01-30/shipments/track).
     * Informe ao menos um identificador na query.
     *
     * @param  array<string, mixed>  $query  id, acsin, aftn, containerNumber,
     *   houseBillOfLadingNumber, carrierTracking.trackingNumber,
     *   carrierTracking.carrierCode
     * @param  string|null  $acceptLanguage  Header `Accept-Language` (ex.: pt-BR)
     */
    public function getShipmentTracking(array $query = [], ?string $acceptLanguage = null): array
    {
        return $this->client->get(
            '/tracking/2026-01-30/shipments/track',
            $query,
            $acceptLanguage !== null ? ['Accept-Language' => $acceptLanguage] : [],
        );
    }
}
