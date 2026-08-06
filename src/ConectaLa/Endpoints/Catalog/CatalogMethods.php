<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Endpoints\Catalog;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\ConectaLa\Bases\BaseMethods;

/** Catálogos (Catalogs): lojas dos catálogos e atualização pelo SKU do fornecedor. */
class CatalogMethods extends BaseMethods
{
    /** Lojas dos catálogos (GET /GetCatalogs/stores). */
    public function stores(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/GetCatalogs/stores', $filters);
    }

    /** Atualiza produto pelo SKU do fornecedor (PATCH /Catalogs/skumanufacturer/{sku}). */
    public function updateBySupplierSku(string $sku, array $body): array
    {
        return $this->makeRequest(HttpMethod::PATCH, "/Catalogs/skumanufacturer/{$sku}", body: $body);
    }
}
