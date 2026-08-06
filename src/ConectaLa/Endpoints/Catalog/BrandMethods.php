<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Endpoints\Catalog;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\ConectaLa\Bases\BaseMethods;

/** Marcas (Brands). */
class BrandMethods extends BaseMethods
{
    /** Lista marcas (GET /Brands). */
    public function list(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/Brands', $filters);
    }

    /** Detalhe de uma marca (GET /Brands/{code}). */
    public function get(string $code): array
    {
        return $this->makeRequest(HttpMethod::GET, "/Brands/{$code}");
    }

    /** Cria uma marca (POST /Brands). */
    public function create(array $brand): array
    {
        return $this->makeRequest(HttpMethod::POST, '/Brands', body: $brand);
    }
}
