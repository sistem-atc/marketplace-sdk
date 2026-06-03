<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Order;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class OrderMethods extends BaseMethods
{
    public function search(
        int|string $sellerId,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        string $sort = 'date_desc',
        int $limit = 50,
        int $offset = 0,
    ): array {
        $query = [
            'seller' => $sellerId,
            'sort' => $sort,
            'limit' => min($limit, 50),
            'offset' => $offset,
        ];

        if ($dateFrom !== null) $query['order.date_created.from'] = $dateFrom;
        if ($dateTo !== null) $query['order.date_created.to'] = $dateTo;

        return $this->makeRequest(HttpMethod::GET, '/orders/search', $query);
    }

    public function get(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}");
    }
}
