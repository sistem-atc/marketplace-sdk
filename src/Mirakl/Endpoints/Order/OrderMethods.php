<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Mirakl\Endpoints\Order;

use SistemAtc\Marketplaces\Mirakl\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class OrderMethods extends BaseMethods
{
    public function listOrders(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/orders', $params);
    }

    public function getOrder(string $orderId): array
    {
        $resp = $this->makeRequest(HttpMethod::GET, '/api/orders', ['order_ids' => $orderId]);
        return $resp['orders'][0] ?? [];
    }

    public function updateTracking(string $orderId, array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/api/orders/{$orderId}/tracking", [], $data);
    }
}
