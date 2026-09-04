<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Category;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Categorias (`/v1/categoria`). Paginação `limit/offset`; `id_externo=1` faz a
 * API buscar pelo id externo em vez do id da LI.
 */
class CategoryMethods extends BaseMethods
{
    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function list(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'categoria/', array_merge(['limit' => $limit, 'offset' => $offset], $filters));
    }

    /** @return array<string,mixed> */
    public function get(int|string $id, bool $byExternalId = false): array
    {
        return $this->makeRequest(HttpMethod::GET, "categoria/{$id}/", $byExternalId ? ['id_externo' => 1] : []);
    }

    /**
     * Subconjunto: ids separados por `;` no path.
     *
     * @param array<int,int|string> $ids
     * @return array<string,mixed>
     */
    public function getSet(array $ids, bool $byExternalId = false): array
    {
        return $this->makeRequest(HttpMethod::GET, 'categoria/set/'.implode(';', $ids).'/', $byExternalId ? ['id_externo' => 1] : []);
    }

    /**
     * @param array<string,mixed> $data nome, descricao, categoria_pai (resource_uri), id_externo
     * @return array<string,mixed>
     */
    public function create(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, 'categoria/', [], $data);
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function update(int|string $id, array $data, bool $byExternalId = false): array
    {
        return $this->makeRequest(HttpMethod::PUT, "categoria/{$id}/", $byExternalId ? ['id_externo' => 1] : [], $data);
    }
}
