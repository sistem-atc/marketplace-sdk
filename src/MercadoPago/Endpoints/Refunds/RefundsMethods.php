<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Refunds;

use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class RefundsMethods extends BaseMethods
{
    /**
     * Realiza o reembolso total ou parcial de um pagamento.
     */
    public function create(int|string $paymentId, ?float $amount = null): array
    {
        $body = $amount !== null ? ['amount' => $amount] : [];
        return $this->makeRequest(HttpMethod::POST, "/v1/payments/{$paymentId}/refunds", [], $body);
    }

    /**
     * Lista reembolsos de um pagamento.
     */
    public function list(int|string $paymentId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/v1/payments/{$paymentId}/refunds");
    }

    /**
     * Consulta um reembolso especifico.
     */
    public function get(int|string $paymentId, int|string $refundId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/v1/payments/{$paymentId}/refunds/{$refundId}");
    }
}
