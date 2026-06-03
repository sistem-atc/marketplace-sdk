<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Delivery;

use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class DeliveryMethods extends BaseMethods
{
    public function listDeliveries(int $limit = 50, int $offset = 0, ?string $status = null): array
    {
        $query = ['limit' => $limit, '_offset' => $offset];
        if ($status) $query['status'] = $status;
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/deliveries', $query);
    }

    public function getDelivery(string $deliveryId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v1/deliveries/{$deliveryId}");
    }

    public function getDeliveryHistory(string $deliveryId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v1/deliveries/{$deliveryId}/histories");
    }
}
