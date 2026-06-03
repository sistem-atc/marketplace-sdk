<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Payments;

use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class PaymentsMethods extends BaseMethods
{
    public function get(int|string $paymentId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/v1/payments/{$paymentId}");
    }

    public function search(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/v1/payments/search', $filters);
    }
}
