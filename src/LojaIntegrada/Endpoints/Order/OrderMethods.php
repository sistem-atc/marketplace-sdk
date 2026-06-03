<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Order;

use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class OrderMethods extends BaseMethods
{
    public function listOrders(int $limit = 50, int $offset = 0, string $orderBy = '-data_criacao', array $extraFilters = []): array
    {
        $query = array_merge(['limit' => min($limit, 50), 'offset' => $offset, 'order_by' => $orderBy], $extraFilters);
        return $this->makeRequest(HttpMethod::GET, 'pedido/search/', $query);
    }

    public function getOrderByNumero(int|string $numero): array
    {
        return $this->makeRequest(HttpMethod::GET, "pedido/{$numero}/");
    }
}
