<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\OrderStatus;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Situações de pedido (`/v1/situacao`, `/v1/situacao/pedido/{numero}`,
 * `/v1/situacao_historico/search`). Paginação `limit/offset`.
 */
class OrderStatusMethods extends BaseMethods
{
    /**
     * Catálogo de situações (codigo, nome, aprovado, cancelado, final).
     *
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function list(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'situacao/', array_merge(['limit' => $limit, 'offset' => $offset], $filters));
    }

    /** Situação atual de um pedido (pelo número). @return array<string,mixed> */
    public function getForOrder(int|string $numero): array
    {
        return $this->makeRequest(HttpMethod::GET, "situacao/pedido/{$numero}/");
    }

    /** Altera a situação pelo `codigo` (ex.: pedido_enviado). @return array<string,mixed> */
    public function updateForOrder(int|string $numero, string $codigo): array
    {
        return $this->makeRequest(HttpMethod::PUT, "situacao/pedido/{$numero}/", [], ['codigo' => $codigo]);
    }

    /**
     * Histórico de situações: por `numero` do pedido OU por `id_externo`.
     *
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function history(int|string|null $numero = null, int|string|null $idExterno = null, array $filters = []): array
    {
        $query = $filters;
        if ($numero !== null) {
            $query['numero'] = $numero;
        }
        if ($idExterno !== null) {
            $query['id_externo'] = $idExterno;
        }

        return $this->makeRequest(HttpMethod::GET, 'situacao_historico/search/', $query);
    }
}
