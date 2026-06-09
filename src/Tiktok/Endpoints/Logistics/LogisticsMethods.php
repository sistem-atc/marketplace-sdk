<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Logistics;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class LogisticsMethods extends BaseMethods
{
    public function getShippingProviders(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/logistics/v202309/shipping_providers');
    }

    public function getShippingInfo(string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/api/logistics/v202309/orders/{$orderId}/shipping_info");
    }

    public function shipOrder(string $orderId, array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, "/api/logistics/v202309/orders/{$orderId}/ship", [], $data);
    }
}
