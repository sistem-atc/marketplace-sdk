<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Price;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Preços (`/v1/produto_preco`). Paginação `limit/offset`; o id do preço é o id do produto;
 * `id_externo=1` busca pelo id externo.
 */
class PriceMethods extends BaseMethods
{
    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function list(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'produto_preco/', array_merge(['limit' => $limit, 'offset' => $offset], $filters));
    }

    /** @return array<string,mixed> */
    public function get(int|string $produtoId, bool $byExternalId = false): array
    {
        return $this->makeRequest(HttpMethod::GET, "produto_preco/{$produtoId}/", $byExternalId ? ['id_externo' => 1] : []);
    }

    /**
     * @param array<int,int|string> $ids ids separados por `;` no path
     * @return array<string,mixed>
     */
    public function getSet(array $ids, bool $byExternalId = false): array
    {
        return $this->makeRequest(HttpMethod::GET, 'produto_preco/set/'.implode(';', $ids).'/', $byExternalId ? ['id_externo' => 1] : []);
    }

    /**
     * @param array<string,mixed> $data cheio, promocional, custo, sob_consulta
     * @return array<string,mixed>
     */
    public function update(int|string $produtoId, array $data, bool $byExternalId = false): array
    {
        return $this->makeRequest(HttpMethod::PUT, "produto_preco/{$produtoId}/", $byExternalId ? ['id_externo' => 1] : [], $data);
    }
}
