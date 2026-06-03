<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Payment;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class PaymentMethods extends BaseMethods
{
    public function getEscrowDetail(string $orderSn): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_escrow_detail', ['order_sn' => $orderSn]);
        $resp = $response['response'] ?? [];
        return [
            'order_sn' => (string) ($resp['order_sn'] ?? $orderSn),
            'order_income' => $resp['order_income'] ?? [],
            'raw' => $resp,
        ];
    }
}
