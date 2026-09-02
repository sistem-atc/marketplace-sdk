<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Refunds;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;

/**
 * Refunds — reembolsos de um pagamento (espelha `PaymentRefundClient`).
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/chargebacks/_payments_id_refunds/post
 */
class RefundsMethods extends BaseMethods
{
    /**
     * Reembolso total (`$amount` null) ou parcial. Aceita chave de
     * idempotencia propria pra que um retry nao reembolse duas vezes.
     *
     * @return array<string, mixed>
     */
    public function create(int|string $paymentId, ?float $amount = null, ?string $idempotencyKey = null): array
    {
        return $this->makeRequest(
            method: HttpMethod::POST,
            path: "/v1/payments/{$paymentId}/refunds",
            body: $amount !== null ? ['amount' => $amount] : [],
            headers: $idempotencyKey ? ['X-Idempotency-Key' => $idempotencyKey] : [],
        );
    }

    /**
     * Atalho semantico do SDK oficial (`refundTotal`).
     *
     * @return array<string, mixed>
     */
    public function refundTotal(int|string $paymentId, ?string $idempotencyKey = null): array
    {
        return $this->create($paymentId, null, $idempotencyKey);
    }

    /**
     * Lista reembolsos de um pagamento.
     *
     * @return array<int, array<string, mixed>>
     */
    public function list(int|string $paymentId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/v1/payments/{$paymentId}/refunds");
    }

    /**
     * Consulta um reembolso especifico.
     *
     * @return array<string, mixed>
     */
    public function get(int|string $paymentId, int|string $refundId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/v1/payments/{$paymentId}/refunds/{$refundId}");
    }
}
