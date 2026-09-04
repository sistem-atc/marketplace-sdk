<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\FinancialAnalysis;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;

/**
 * Analise financeira / DRE do seller (/seller/v1/financial-analysis).
 *
 * Pedidos com as transacoes (comissao, frete, repasse, penalidades) e
 * transacoes por data ou por batch. Duas paginacoes: `_limit`/`_offset`
 * (classica) OU cursor — passe `_paginate=cursor` na 1a chamada e depois o
 * `_next_token` devolvido no link `next` (ignora `_offset`). Cursor e' o
 * recomendado pra janelas grandes.
 */
class FinancialAnalysisMethods extends BaseMethods
{
    /**
     * Pedidos com visao financeira (GET /seller/v1/financial-analysis/orders).
     *
     * Filtros: `purchased_at__gte|lte`, `updated_at__gte|lte`, `order_id`;
     * `_sort`, `_paginate=cursor`, `_next_token`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function listOrders(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/financial-analysis/orders', array_merge($filters, [
            '_limit' => $limit,
            '_offset' => $offset,
        ]));
    }

    /**
     * Visao financeira de um pedido (GET /seller/v1/financial-analysis/orders/{order_id}).
     *
     * @return array<string, mixed>
     */
    public function getOrder(string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v1/financial-analysis/orders/{$orderId}");
    }

    /**
     * Transacoes por data de criacao (GET /seller/v1/financial-analysis/transactions).
     * Filtros: `created_at__gte|lte`; `_sort`, `_paginate`, `_next_token`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function listTransactions(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/financial-analysis/transactions', array_merge($filters, [
            '_limit' => $limit,
            '_offset' => $offset,
        ]));
    }

    /**
     * Transacoes de um batch de repasse
     * (GET /seller/v1/financial-analysis/transactions/{batch_id}).
     *
     * @return array<string, mixed>
     */
    public function listTransactionsByBatch(string $batchId, int $limit = 50, int $offset = 0, ?string $sort = null): array
    {
        $query = ['_limit' => $limit, '_offset' => $offset];
        if ($sort !== null) $query['_sort'] = $sort;

        return $this->makeRequest(HttpMethod::GET, "/seller/v1/financial-analysis/transactions/{$batchId}", $query);
    }
}
