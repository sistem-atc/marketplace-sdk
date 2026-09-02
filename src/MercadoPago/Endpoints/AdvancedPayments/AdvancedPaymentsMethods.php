<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\AdvancedPayments;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\AdvancedPayments\AdvancedPaymentResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\AdvancedPayments\AdvancedPaymentSearchResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\AdvancedPayments\DisbursementRefundListResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\AdvancedPayments\DisbursementRefundResponseDTO;

/**
 * Advanced Payments — split de pagamento entre varios recebedores
 * (marketplace: cada `disbursement` e' uma cota pra um collector). Inclui
 * os reembolsos por disbursement (`DisbursementRefundClient` no SDK).
 *
 * O MP marca esta API como legada em favor de Orders v2 com split; mantida
 * aqui por paridade com o SDK oficial.
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/advanced_payments/_advanced_payments/post
 */
class AdvancedPaymentsMethods extends BaseMethods
{
    /**
     * @param  array<string, mixed>  $payload  payer, payments[], disbursements[], external_reference, capture...
     */
    public function create(array $payload, ?string $idempotencyKey = null): AdvancedPaymentResponseDTO
    {
        return AdvancedPaymentResponseDTO::fromArray($this->makeRequest(
            method: HttpMethod::POST,
            path: '/v1/advanced_payments',
            body: $payload,
            headers: $idempotencyKey ? ['X-Idempotency-Key' => $idempotencyKey] : [],
        ));
    }

    public function get(int|string $advancedPaymentId): AdvancedPaymentResponseDTO
    {
        return AdvancedPaymentResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, "/v1/advanced_payments/{$advancedPaymentId}"));
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function search(array $filters = []): AdvancedPaymentSearchResponseDTO
    {
        return AdvancedPaymentSearchResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/v1/advanced_payments/search', $filters));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(int|string $advancedPaymentId, array $payload): AdvancedPaymentResponseDTO
    {
        return AdvancedPaymentResponseDTO::fromArray($this->makeRequest(HttpMethod::PUT, "/v1/advanced_payments/{$advancedPaymentId}", body: $payload));
    }

    public function capture(int|string $advancedPaymentId): AdvancedPaymentResponseDTO
    {
        return $this->update($advancedPaymentId, ['capture' => true]);
    }

    public function cancel(int|string $advancedPaymentId): AdvancedPaymentResponseDTO
    {
        return $this->update($advancedPaymentId, ['status' => 'cancelled']);
    }

    /**
     * Muda a data de liberacao de TODOS os disbursements.
     *
     * @param  string  $releaseDate  ISO-8601
     */
    public function updateReleaseDate(int|string $advancedPaymentId, string $releaseDate): AdvancedPaymentResponseDTO
    {
        return AdvancedPaymentResponseDTO::fromArray($this->makeRequest(
            HttpMethod::POST,
            "/v1/advanced_payments/{$advancedPaymentId}/disburses",
            body: ['money_release_date' => $releaseDate],
        ));
    }

    // ── Disbursement refunds ─────────────────────────────────────────────

    /**
     * Reembolsos por disbursement ja' feitos — a lista vem em `refunds`.
     */
    public function listRefunds(int|string $advancedPaymentId): DisbursementRefundListResponseDTO
    {
        return DisbursementRefundListResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, "/v1/advanced_payments/{$advancedPaymentId}/refunds"));
    }

    /**
     * Reembolsa TODOS os disbursements (total se body vazio).
     *
     * @param  array<string, mixed>  $payload
     */
    public function refundAll(int|string $advancedPaymentId, array $payload = []): DisbursementRefundListResponseDTO
    {
        return DisbursementRefundListResponseDTO::fromArray($this->makeRequest(HttpMethod::POST, "/v1/advanced_payments/{$advancedPaymentId}/refunds", body: $payload));
    }

    /**
     * Reembolsa UM disbursement, parcial ou total.
     */
    public function refundDisbursement(int|string $advancedPaymentId, int|string $disbursementId, float $amount): DisbursementRefundResponseDTO
    {
        return DisbursementRefundResponseDTO::fromArray($this->makeRequest(
            HttpMethod::POST,
            "/v1/advanced_payments/{$advancedPaymentId}/disbursements/{$disbursementId}/refunds",
            body: ['amount' => $amount],
        ));
    }
}
