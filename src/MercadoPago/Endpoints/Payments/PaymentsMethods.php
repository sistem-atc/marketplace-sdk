<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Payments;

use Generator;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;

/**
 * Payments — pagamento individual do Mercado Pago.
 *
 * Diferente do SettlementMethods (CSV agregado do periodo), aqui e' UM
 * pagamento: debug de repasse que nao apareceu, validar status apos
 * webhook, achar o payment_id pelo external_reference (= order_id do
 * marketplace).
 *
 * Espelha `PaymentClient` + `PaymentRefundClient` do SDK oficial. Os
 * reembolsos ficam em RefundsMethods.
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/payments/_payments/post
 */
class PaymentsMethods extends BaseMethods
{
    /**
     * Cria um pagamento. Payload livre (transaction_amount, token,
     * payment_method_id, payer, installments, external_reference...).
     * A chave de idempotencia e' obrigatoria aqui: sem ela o MP devolve 400.
     * Passe a sua em `$idempotencyKey` (ex.: id do pedido) pra que um retry
     * devolva o MESMO pagamento em vez de cobrar duas vezes.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function create(array $payload, ?string $idempotencyKey = null): array
    {
        return $this->makeRequest(
            method: HttpMethod::POST,
            path: '/v1/payments',
            body: $payload,
            headers: $idempotencyKey ? ['X-Idempotency-Key' => $idempotencyKey] : [],
        );
    }

    /**
     * Detalhe de um pagamento. Resposta inclui status/status_detail,
     * transaction_amount (bruto), net_received_amount (liquido), fee_details,
     * external_reference, order.id (merchant_order) e date_released.
     *
     * @return array<string, mixed>
     */
    public function get(int|string $paymentId): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: "/v1/payments/{$paymentId}",
        );
    }

    /**
     * Atualiza campos mutaveis (metadata, external_reference, description,
     * date_of_expiration...).
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function update(int|string $paymentId, array $payload): array
    {
        return $this->makeRequest(
            method: HttpMethod::PUT,
            path: "/v1/payments/{$paymentId}",
            body: $payload,
        );
    }

    /**
     * Cancela um pagamento ainda nao aprovado (pending/in_process).
     * Pagamento aprovado NAO cancela — e' reembolso (RefundsMethods).
     *
     * @return array<string, mixed>
     */
    public function cancel(int|string $paymentId): array
    {
        return $this->update($paymentId, ['status' => 'cancelled']);
    }

    /**
     * Captura um pagamento autorizado (capture=false na criacao).
     * `$amount` null = captura o valor total autorizado; menor = parcial.
     *
     * @return array<string, mixed>
     */
    public function capture(int|string $paymentId, ?float $amount = null): array
    {
        $body = ['capture' => true];

        if ($amount !== null) {
            $body['transaction_amount'] = $amount;
        }

        return $this->update($paymentId, $body);
    }

    /**
     * Busca paginada. Filtros: external_reference, status, range (date_created,
     * date_last_updated, date_approved...), begin_date/end_date, sort,
     * criteria, limit, offset.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function search(array $filters = []): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: '/v1/payments/search',
            query: $filters,
        );
    }

    /**
     * Todas as paginas do search, item a item.
     *
     * @param  array<string, mixed>  $filters
     * @return Generator<int, array<string, mixed>>
     */
    public function searchAll(array $filters = [], int $limit = 100): Generator
    {
        return $this->paginate('/v1/payments/search', $filters, $limit);
    }

    /**
     * Lookup direto por external_reference (= order_id no marketplace).
     *
     * @return array<int, array<string, mixed>>
     */
    public function findByExternalReference(string $externalRef): array
    {
        $resp = $this->search(['external_reference' => $externalRef]);

        return $resp['results'] ?? [];
    }
}
