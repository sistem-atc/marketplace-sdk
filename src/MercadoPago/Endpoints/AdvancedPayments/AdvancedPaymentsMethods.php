<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\AdvancedPayments;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;

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
     * @return array<string, mixed>
     */
    public function create(array $payload, ?string $idempotencyKey = null): array
    {
        return $this->makeRequest(
            method: HttpMethod::POST,
            path: '/v1/advanced_payments',
            body: $payload,
            headers: $idempotencyKey ? ['X-Idempotency-Key' => $idempotencyKey] : [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function get(int|string $advancedPaymentId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/v1/advanced_payments/{$advancedPaymentId}");
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function search(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/v1/advanced_payments/search', $filters);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function update(int|string $advancedPaymentId, array $payload): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/v1/advanced_payments/{$advancedPaymentId}", body: $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function capture(int|string $advancedPaymentId): array
    {
        return $this->update($advancedPaymentId, ['capture' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    public function cancel(int|string $advancedPaymentId): array
    {
        return $this->update($advancedPaymentId, ['status' => 'cancelled']);
    }

    /**
     * Muda a data de liberacao de TODOS os disbursements.
     *
     * @param  string  $releaseDate  ISO-8601
     * @return array<string, mixed>
     */
    public function updateReleaseDate(int|string $advancedPaymentId, string $releaseDate): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/v1/advanced_payments/{$advancedPaymentId}/disburses",
            body: ['money_release_date' => $releaseDate],
        );
    }

    // ── Disbursement refunds ─────────────────────────────────────────────

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listRefunds(int|string $advancedPaymentId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/v1/advanced_payments/{$advancedPaymentId}/refunds");
    }

    /**
     * Reembolsa TODOS os disbursements (total se body vazio).
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function refundAll(int|string $advancedPaymentId, array $payload = []): array
    {
        return $this->makeRequest(HttpMethod::POST, "/v1/advanced_payments/{$advancedPaymentId}/refunds", body: $payload);
    }

    /**
     * Reembolsa UM disbursement, parcial ou total.
     *
     * @return array<string, mixed>
     */
    public function refundDisbursement(int|string $advancedPaymentId, int|string $disbursementId, float $amount): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/v1/advanced_payments/{$advancedPaymentId}/disbursements/{$disbursementId}/refunds",
            body: ['amount' => $amount],
        );
    }
}
