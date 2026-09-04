<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Support\FlattensCsvQuery;

/**
 * Vehicles API 2024-11-01 (Automotive) — catálogo de veículos da Amazon pra
 * montar tabelas de compatibilidade (fitment) de peças automotivas.
 */
class Vehicles
{
    use FlattensCsvQuery;

    private const BASE = '/catalog/2024-11-01/automotive';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Lista veículos do catálogo (GET /catalog/2024-11-01/automotive/vehicles).
     * `vehicleType`: CAR|MOTORBIKE. Query opcional: pageToken, updatedAfter
     * (ISO 8601 — só veículos alterados/adicionados depois). Retorna na raiz
     * `vehicles[]` + `pagination` {nextToken, previousToken} (passar em
     * `pageToken`). Rate limit: não declarado no modelo.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function getVehicles(string $marketplaceId, string $vehicleType, array $query = []): array
    {
        return $this->client->get(
            self::BASE.'/vehicles',
            $this->csv(['marketplaceId' => $marketplaceId, 'vehicleType' => $vehicleType] + $query),
        );
    }
}
