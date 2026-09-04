<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Stock;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Estoque (`/v1/produto_estoque`). Paginação `limit/offset`; o id do estoque é o id do produto.
 */
class StockMethods extends BaseMethods
{
    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function list(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'produto_estoque/', array_merge(['limit' => $limit, 'offset' => $offset], $filters));
    }

    /** @return array<string,mixed> */
    public function get(int|string $produtoId, bool $byExternalId = false): array
    {
        return $this->makeRequest(HttpMethod::GET, "produto_estoque/{$produtoId}/", $byExternalId ? ['id_externo' => 1] : []);
    }

    /**
     * @param array<int,int|string> $ids ids separados por `;` no path (doc antiga)
     * @return array<string,mixed>
     */
    public function getSet(array $ids, bool $byExternalId = false): array
    {
        return $this->makeRequest(HttpMethod::GET, 'produto_estoque/set/'.implode(';', $ids).'/', $byExternalId ? ['id_externo' => 1] : []);
    }

    /**
     * @param array<string,mixed> $data gerenciado, quantidade, situacao_em_estoque, situacao_sem_estoque
     * @return array<string,mixed>
     */
    public function update(int|string $produtoId, array $data, bool $byExternalId = false): array
    {
        return $this->makeRequest(HttpMethod::PUT, "produto_estoque/{$produtoId}/", $byExternalId ? ['id_externo' => 1] : [], $data);
    }

    /** Atalho: só a quantidade. @return array<string,mixed> */
    public function updateQuantity(int|string $produtoId, int $quantidade): array
    {
        return $this->update($produtoId, ['quantidade' => $quantidade]);
    }
}
