<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Order;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class OrderMethods extends BaseMethods
{
    public function getOrderList(array $params = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/api/order/get_order_list',
            $params
        );
    }
}
