<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Order;

use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\DTO\Response\Order\OrderResponseDTO;

class OrderMethods extends BaseMethods
{
    public function listOrders(int $limit = 50, int $offset = 0, ?string $status = null): array
    {
        $query = ['limit' => min($limit, 100), '_offset' => $offset];
        if ($status !== null) $query['status'] = $status;
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/orders', $query);
    }

    public function getOrder(string $orderId): OrderResponseDTO
    {
        return OrderResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::GET, "/seller/v1/orders/{$orderId}"),
        );
    }


    /**
     * Busca de pedidos com todos os filtros da doc (GET /seller/v1/orders).
     *
     * Filtros: `status`, `code`, `purchased_at__gte|lte`, `updated_at__gte|lte`
     * (ISO 8601); paginacao `_limit` (default 20) / `_offset`; `_sort`
     * (`purchased_at:asc|desc`). Devolve o envelope `meta` + `results`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function search(array $filters = [], int $limit = 20, int $offset = 0, ?string $sort = null): array
    {
        $query = array_merge($filters, ['_limit' => $limit, '_offset' => $offset]);
        if ($sort !== null) $query['_sort'] = $sort;

        return $this->makeRequest(HttpMethod::GET, '/seller/v1/orders', $query);
    }

    /**
     * Pedido cru (array) por codigo — GET /seller/v1/orders/{code}.
     * `updated_at__ge` devolve 304/vazio se nao mudou desde a data.
     *
     * @return array<string, mixed>
     */
    public function getOrderRaw(string $code, ?string $updatedAtGe = null): array
    {
        $query = $updatedAtGe !== null ? ['updated_at__ge' => $updatedAtGe] : [];

        return $this->makeRequest(HttpMethod::GET, "/seller/v1/orders/{$code}", $query);
    }
}
