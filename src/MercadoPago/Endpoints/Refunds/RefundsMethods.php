<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Refunds;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Refunds\PaymentRefundResponseDTO;

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
     */
    public function create(int|string $paymentId, ?float $amount = null, ?string $idempotencyKey = null): PaymentRefundResponseDTO
    {
        return PaymentRefundResponseDTO::fromArray($this->makeRequest(
            method: HttpMethod::POST,
            path: "/v1/payments/{$paymentId}/refunds",
            body: $amount !== null ? ['amount' => $amount] : [],
            headers: $idempotencyKey ? ['X-Idempotency-Key' => $idempotencyKey] : [],
        ));
    }

    /**
     * Atalho semantico do SDK oficial (`refundTotal`).
     */
    public function refundTotal(int|string $paymentId, ?string $idempotencyKey = null): PaymentRefundResponseDTO
    {
        return $this->create($paymentId, null, $idempotencyKey);
    }

    /**
     * Lista reembolsos de um pagamento.
     *
     * @return list<PaymentRefundResponseDTO>
     */
    public function list(int|string $paymentId): array
    {
        return $this->hydrateList($this->makeRequest(HttpMethod::GET, "/v1/payments/{$paymentId}/refunds"), PaymentRefundResponseDTO::class);
    }

    /**
     * Consulta um reembolso especifico.
     */
    public function get(int|string $paymentId, int|string $refundId): PaymentRefundResponseDTO
    {
        return PaymentRefundResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, "/v1/payments/{$paymentId}/refunds/{$refundId}"));
    }
}
