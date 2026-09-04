<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Cardan;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;

/**
 * Cardan — compatibilidade de autopecas com veiculos
 * (/seller/v1/portfolios/vehicles). Navegacao fabricante → modelo → ano →
 * versao; o SKU e' vinculado a `vehicle_version_ids`. Paginacao
 * `_limit`/`_offset`/`_sort` nas listagens.
 */
class CardanMethods extends BaseMethods
{
    /**
     * Fabricantes (GET /seller/v1/portfolios/vehicles/manufacturers).
     *
     * @return array<string, mixed>
     */
    public function listManufacturers(int $limit = 50, int $offset = 0, ?string $sort = null): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/vehicles/manufacturers', $this->page($limit, $offset, $sort));
    }

    /**
     * Modelos do fabricante (GET .../manufacturers/{id}/models).
     *
     * @return array<string, mixed>
     */
    public function listModels(string $manufacturerId, int $limit = 50, int $offset = 0, ?string $sort = null): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/seller/v1/portfolios/vehicles/manufacturers/{$manufacturerId}/models",
            $this->page($limit, $offset, $sort),
        );
    }

    /**
     * Anos do modelo (GET .../models/{id}/years).
     *
     * @return array<string, mixed>
     */
    public function listYears(string $manufacturerId, string $modelId, int $limit = 50, int $offset = 0, ?string $sort = null): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/seller/v1/portfolios/vehicles/manufacturers/{$manufacturerId}/models/{$modelId}/years",
            $this->page($limit, $offset, $sort),
        );
    }

    /**
     * Versoes do ano (GET .../years/{id}/versions) — o id da versao e' o que entra na compatibilidade.
     *
     * @return array<string, mixed>
     */
    public function listVersions(
        string $manufacturerId,
        string $modelId,
        string $yearId,
        int $limit = 50,
        int $offset = 0,
        ?string $sort = null,
    ): array {
        return $this->makeRequest(
            HttpMethod::GET,
            "/seller/v1/portfolios/vehicles/manufacturers/{$manufacturerId}/models/{$modelId}/years/{$yearId}/versions",
            $this->page($limit, $offset, $sort),
        );
    }

    /**
     * Cria compatibilidades do SKU (POST /seller/v1/portfolios/vehicles/compatibilities).
     *
     * @param list<string> $vehicleVersionIds
     * @return array<string, mixed>
     */
    public function createCompatibilities(string $sku, array $vehicleVersionIds, ?string $partNumber = null): array
    {
        return $this->makeRequest(HttpMethod::POST, '/seller/v1/portfolios/vehicles/compatibilities', [], $this->compatibilityBody($sku, $vehicleVersionIds, $partNumber));
    }

    /**
     * Substitui (upsert) as compatibilidades do SKU (PUT .../compatibilities).
     *
     * @param list<string> $vehicleVersionIds
     * @return array<string, mixed>
     */
    public function upsertCompatibilities(string $sku, array $vehicleVersionIds, ?string $partNumber = null): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/seller/v1/portfolios/vehicles/compatibilities', [], $this->compatibilityBody($sku, $vehicleVersionIds, $partNumber));
    }

    /**
     * Remove todas as compatibilidades do SKU (DELETE .../compatibilities, body {sku}).
     *
     * @return array<string, mixed>
     */
    public function deleteCompatibilities(string $sku): array
    {
        return $this->makeRequest(HttpMethod::DELETE, '/seller/v1/portfolios/vehicles/compatibilities', [], ['sku' => $sku]);
    }

    /** @return array<string, mixed> */
    private function page(int $limit, int $offset, ?string $sort): array
    {
        $query = ['_limit' => $limit, '_offset' => $offset];
        if ($sort !== null) $query['_sort'] = $sort;

        return $query;
    }

    /**
     * @param list<string> $vehicleVersionIds
     * @return array<string, mixed>
     */
    private function compatibilityBody(string $sku, array $vehicleVersionIds, ?string $partNumber): array
    {
        $body = ['sku' => $sku, 'vehicle_version_ids' => array_values($vehicleVersionIds)];
        if ($partNumber !== null) $body['part_number'] = $partNumber;

        return $body;
    }
}
