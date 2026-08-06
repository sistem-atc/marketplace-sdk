<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Endpoints\Catalog;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\ConectaLa\Bases\BaseMethods;

/**
 * Navegações / vitrines (Collections). Na doc usam o host `base_sellercenter`,
 * que na homolog resolve pro mesmo `.../app/Api/V1` — então os paths relativos
 * abaixo batem no cliente padrão. (Se em prod o host divergir, ajustar aqui.)
 */
class CollectionMethods extends BaseMethods
{
    /** Lista navegações (GET /Collections/all). */
    public function list(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/Collections/all', $filters);
    }

    /** Navegação por id (GET /Collections/{id}). */
    public function get(string $collectionId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/Collections/{$collectionId}");
    }

    /** Navegações de um produto (GET /Collections/product/{sku}). */
    public function byProduct(string $sku): array
    {
        return $this->makeRequest(HttpMethod::GET, "/Collections/product/{$sku}");
    }

    /** Atualiza navegações de um produto no marketplace (POST /Collections/product/{sku}/{marketplace}). */
    public function updateByProduct(string $sku, string $marketplace, array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, "/Collections/product/{$sku}/{$marketplace}", body: $body);
    }
}
