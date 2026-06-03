<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Order;

use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class OrderMethods extends BaseMethods
{
    public function listOrders(int $limit = 50, int $offset = 0, ?string $status = null): array
    {
        $query = ['limit' => min($limit, 100), '_offset' => $offset];
        if ($status !== null) $query['status'] = $status;
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/orders', $query);
    }

    public function getOrder(string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v1/orders/{$orderId}");
    }
}
