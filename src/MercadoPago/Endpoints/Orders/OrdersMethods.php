<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Orders;

use Generator;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;

/**
 * Orders v2 — a API nova do MP que unifica online (checkout API) e
 * presencial (Point/QR) num recurso so'. Uma order tem `transactions`
 * (payments) que podem ser adicionadas/alteradas antes de processar.
 *
 * Ids de order e transaction sao strings (ex.: "ORD01J...", "PAY01J...").
 * `paging.total` do search vem como STRING — o paginador da base trata.
 *
 * Espelha `OrderClient` + `OrderTransactionClient`.
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/in-person-payments/orders/create/post
 */
class OrdersMethods extends BaseMethods
{
    /**
     * @param  array<string, mixed>  $payload  type (online|point|qr), total_amount, external_reference, transactions, payer, processing_mode...
     * @return array<string, mixed>
     */
    public function create(array $payload, ?string $idempotencyKey = null): array
    {
        return $this->makeRequest(
            method: HttpMethod::POST,
            path: '/v1/orders',
            body: $payload,
            headers: $idempotencyKey ? ['X-Idempotency-Key' => $idempotencyKey] : [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/v1/orders/'.rawurlencode($orderId));
    }

    /**
     * O search de Orders v2 NAO tem sufixo `/search`: e' GET /v1/orders
     * com filtros na query.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function search(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/v1/orders', $filters);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Generator<int, array<string, mixed>>
     */
    public function searchAll(array $filters = [], int $limit = 100): Generator
    {
        return $this->paginate('/v1/orders', $filters, $limit);
    }

    /**
     * Captura uma order criada em modo manual (`capture_mode: manual`).
     *
     * @return array<string, mixed>
     */
    public function capture(string $orderId): array
    {
        return $this->makeRequest(HttpMethod::POST, '/v1/orders/'.rawurlencode($orderId).'/capture');
    }

    /**
     * @return array<string, mixed>
     */
    public function cancel(string $orderId): array
    {
        return $this->makeRequest(HttpMethod::POST, '/v1/orders/'.rawurlencode($orderId).'/cancel');
    }

    /**
     * Processa uma order criada em modo manual (`processing_mode: manual`)
     * depois que as transactions foram montadas.
     *
     * @return array<string, mixed>
     */
    public function process(string $orderId): array
    {
        return $this->makeRequest(HttpMethod::POST, '/v1/orders/'.rawurlencode($orderId).'/process');
    }

    /**
     * Reembolso total (sem body) ou parcial (`transactions[]` com amount).
     *
     * @param  array<string, mixed>|null  $payload
     * @return array<string, mixed>
     */
    public function refund(string $orderId, ?array $payload = null, ?string $idempotencyKey = null): array
    {
        return $this->makeRequest(
            method: HttpMethod::POST,
            path: '/v1/orders/'.rawurlencode($orderId).'/refund',
            body: $payload ?? [],
            headers: $idempotencyKey ? ['X-Idempotency-Key' => $idempotencyKey] : [],
        );
    }

    // ── Transactions ─────────────────────────────────────────────────────

    /**
     * @param  array<string, mixed>  $payload  payments[] com amount, payment_method...
     * @return array<string, mixed>
     */
    public function createTransaction(string $orderId, array $payload): array
    {
        return $this->makeRequest(HttpMethod::POST, '/v1/orders/'.rawurlencode($orderId).'/transactions', body: $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function updateTransaction(string $orderId, string $transactionId, array $payload): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            '/v1/orders/'.rawurlencode($orderId).'/transactions/'.rawurlencode($transactionId),
            body: $payload,
        );
    }

    /**
     * Devolve 204 sem corpo — array vazio no sucesso.
     *
     * @return array<string, mixed>
     */
    public function deleteTransaction(string $orderId, string $transactionId): array
    {
        return $this->makeRequest(
            HttpMethod::DELETE,
            '/v1/orders/'.rawurlencode($orderId).'/transactions/'.rawurlencode($transactionId),
        );
    }
}
