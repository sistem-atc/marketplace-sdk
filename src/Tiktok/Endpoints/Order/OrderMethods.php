<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Order;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use InvalidArgumentException;

/**
 * Endpoints de pedidos TikTok Shop (OpenAPI versionada /order/{version}/...).
 *
 * Pagination: cursor via `page_token`; `next_page_token` na resposta indica
 * se ha mais. `time_ge`/`time_lt` em segundos epoch (NAO milliseconds).
 * Bulk detail: max 50 order_ids por chamada.
 */
class OrderMethods extends BaseMethods
{
    private const MAX_DETAIL_BATCH = 50;

    private const MAX_PAGE_SIZE = 100;

    private const MAX_TIME_RANGE_SECONDS = 30 * 24 * 60 * 60; // 30 dias

    private const DEFAULT_VERSION = '202309';

    /**
     * Lista pedidos numa janela de tempo (create_time ou update_time),
     * paginada por page_token.
     *
     * @param  string  $sortField  'create_time' | 'update_time'
     * @param  string|null  $orderStatus  UNPAID | AWAITING_SHIPMENT | AWAITING_COLLECTION | IN_TRANSIT | DELIVERED | COMPLETED | CANCELLED
     * @return array{orders: array<int, array<string, mixed>>, next_page_token: string|null, total_count: int|null}
     */
    public function getOrderList(
        int $timeGe,
        int $timeLt,
        string $sortField = 'create_time',
        ?string $orderStatus = null,
        int $pageSize = 50,
        string $pageToken = '',
        string $version = self::DEFAULT_VERSION,
    ): array {
        if ($timeLt - $timeGe > self::MAX_TIME_RANGE_SECONDS) {
            throw new InvalidArgumentException(sprintf(
                'Janela maior que %d dias (TikTok max). Use janelas menores.',
                self::MAX_TIME_RANGE_SECONDS / 86400,
            ));
        }

        $query = [
            'page_size' => min($pageSize, self::MAX_PAGE_SIZE),
        ];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $body = [
            'order_status' => $orderStatus,
            $sortField.'_ge' => $timeGe,
            $sortField.'_lt' => $timeLt,
        ];
        // TikTok recusa null em filtros
        $body = array_filter($body, fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/order/{$version}/orders/search",
            $query,
            $body,
        );

        $data = $response['data'] ?? [];

        return [
            'orders' => $data['orders'] ?? [],
            'next_page_token' => $data['next_page_token'] ?? null,
            'total_count' => $data['total_count'] ?? null,
        ];
    }

    /**
     * Detalhes de pedidos por lote. Max 50 order_ids por chamada.
     *
     * @param  array<int, string>  $orderIds
     * @return array<int, array<string, mixed>>
     */
    public function getOrderDetail(array $orderIds, string $version = self::DEFAULT_VERSION): array
    {
        if (empty($orderIds)) {
            return [];
        }

        if (count($orderIds) > self::MAX_DETAIL_BATCH) {
            throw new InvalidArgumentException(sprintf(
                'Max %d order_ids por chamada (recebeu %d). Fragmente o lote.',
                self::MAX_DETAIL_BATCH,
                count($orderIds),
            ));
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/order/{$version}/orders",
            ['ids' => implode(',', $orderIds)],
        );

        return $response['data']['orders'] ?? [];
    }
}
