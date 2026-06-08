<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Mirakl\Endpoints\Order;

use SistemAtc\Marketplaces\Mirakl\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

/**
 * Endpoints de pedidos Mirakl (Seller API): OR11 (list), OR12 (detail),
 * OR23 (tracking). Paginacao OR11 por offset/max + cursor de data.
 */
class OrderMethods extends BaseMethods
{
    /** Page size maximo aceito pela OR11. */
    private const MAX_PAGE_SIZE = 100;

    /** Guard anti-runaway: nunca passa disso num unico ciclo. */
    private const MAX_PAGES = 500;

    /**
     * OR11 — UMA pagina de pedidos.
     *
     * @param  array<string, mixed>  $filters  start_update_date/end_update_date
     *                                         (ISO 8601), order_state_codes, order_ids, offset, max
     * @return array{orders?: array<int, array<string, mixed>>, total_count?: int}
     */
    public function listOrders(array $filters = []): array
    {
        $query = array_merge([
            'offset' => 0,
            'max' => self::MAX_PAGE_SIZE,
        ], $filters);

        $query['max'] = min((int) $query['max'], self::MAX_PAGE_SIZE);

        return $this->makeRequest(HttpMethod::GET, '/api/orders', $query);
    }

    /**
     * OR11 com paginacao automatica — itera todas as paginas (ate total_count
     * ou o hard limit de paginas) e devolve a lista achatada.
     *
     * @param  array<string, mixed>  $filters
     * @return array{orders: array<int, array<string, mixed>>, total_count: int}
     */
    public function listAllOrders(array $filters = []): array
    {
        $max = min((int) ($filters['max'] ?? self::MAX_PAGE_SIZE), self::MAX_PAGE_SIZE);
        $offset = (int) ($filters['offset'] ?? 0);

        $all = [];
        $totalCount = 0;

        for ($page = 0; $page < self::MAX_PAGES; $page++) {
            $resp = $this->listOrders(array_merge($filters, [
                'offset' => $offset,
                'max' => $max,
            ]));

            $orders = $resp['orders'] ?? [];
            $totalCount = (int) ($resp['total_count'] ?? count($orders));

            if (empty($orders)) {
                break;
            }

            foreach ($orders as $order) {
                $all[] = $order;
            }

            $offset += $max;
            if ($offset >= $totalCount) {
                break;
            }
        }

        return ['orders' => $all, 'total_count' => $totalCount];
    }

    /**
     * OR12 — detalhe de UM pedido pelo order_id Mirakl.
     *
     * @return array<string, mixed>
     */
    public function getOrder(string $orderId): array
    {
        $resp = $this->makeRequest(HttpMethod::GET, '/api/orders', ['order_ids' => $orderId]);

        return $resp['orders'][0] ?? [];
    }

    /**
     * OR23 — atualiza tracking (carrier + tracking number) de um pedido.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function updateTracking(string $orderId, array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/api/orders/{$orderId}/tracking", [], $data);
    }
}
