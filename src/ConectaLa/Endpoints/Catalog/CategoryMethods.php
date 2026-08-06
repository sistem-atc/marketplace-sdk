<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Endpoints\Catalog;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\ConectaLa\Bases\BaseMethods;

/** Categorias e seus atributos (o que define os campos exigidos do SKU). */
class CategoryMethods extends BaseMethods
{
    /** Lista categorias (GET /Categories). */
    public function list(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/Categories', $filters);
    }

    /** Detalhe de uma categoria (GET /Categories/{code}). */
    public function get(string $code): array
    {
        return $this->makeRequest(HttpMethod::GET, "/Categories/{$code}");
    }

    /** Atributos exigidos por uma categoria (GET /Categories/attributes/{code}). */
    public function attributes(string $code): array
    {
        return $this->makeRequest(HttpMethod::GET, "/Categories/attributes/{$code}");
    }
}
