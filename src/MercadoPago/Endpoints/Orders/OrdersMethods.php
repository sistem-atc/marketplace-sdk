<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Orders;

use Generator;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders\OrderResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders\OrderSearchResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders\OrderTransactionUpdateResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders\OrderTransactionsResponseDTO;

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
     */
    public function create(array $payload, ?string $idempotencyKey = null): OrderResponseDTO
    {
        return OrderResponseDTO::fromArray($this->makeRequest(
            method: HttpMethod::POST,
            path: '/v1/orders',
            body: $payload,
            headers: $idempotencyKey ? ['X-Idempotency-Key' => $idempotencyKey] : [],
        ));
    }

    public function get(string $orderId): OrderResponseDTO
    {
        return OrderResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/v1/orders/'.rawurlencode($orderId)));
    }

    /**
     * O search de Orders v2 NAO tem sufixo `/search`: e' GET /v1/orders
     * com filtros na query.
     *
     * @param  array<string, mixed>  $filters
     */
    public function search(array $filters = []): OrderSearchResponseDTO
    {
        return OrderSearchResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/v1/orders', $filters));
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Generator<int, OrderResponseDTO>
     */
    public function searchAll(array $filters = [], int $limit = 100): Generator
    {
        return $this->paginate('/v1/orders', $filters, $limit, map: OrderResponseDTO::fromArray(...));
    }

    /**
     * Captura uma order criada em modo manual (`capture_mode: manual`).
     */
    public function capture(string $orderId): OrderResponseDTO
    {
        return OrderResponseDTO::fromArray($this->makeRequest(HttpMethod::POST, '/v1/orders/'.rawurlencode($orderId).'/capture'));
    }

    public function cancel(string $orderId): OrderResponseDTO
    {
        return OrderResponseDTO::fromArray($this->makeRequest(HttpMethod::POST, '/v1/orders/'.rawurlencode($orderId).'/cancel'));
    }

    /**
     * Processa uma order criada em modo manual (`processing_mode: manual`)
     * depois que as transactions foram montadas.
     */
    public function process(string $orderId): OrderResponseDTO
    {
        return OrderResponseDTO::fromArray($this->makeRequest(HttpMethod::POST, '/v1/orders/'.rawurlencode($orderId).'/process'));
    }

    /**
     * Reembolso total (sem body) ou parcial (`transactions[]` com amount).
     *
     * @param  array<string, mixed>|null  $payload
     */
    public function refund(string $orderId, ?array $payload = null, ?string $idempotencyKey = null): OrderResponseDTO
    {
        return OrderResponseDTO::fromArray($this->makeRequest(
            method: HttpMethod::POST,
            path: '/v1/orders/'.rawurlencode($orderId).'/refund',
            body: $payload ?? [],
            headers: $idempotencyKey ? ['X-Idempotency-Key' => $idempotencyKey] : [],
        ));
    }

    // ── Transactions ─────────────────────────────────────────────────────

    /**
     * @param  array<string, mixed>  $payload  payments[] com amount, payment_method...
     */
    public function createTransaction(string $orderId, array $payload): OrderTransactionsResponseDTO
    {
        return OrderTransactionsResponseDTO::fromArray($this->makeRequest(HttpMethod::POST, '/v1/orders/'.rawurlencode($orderId).'/transactions', body: $payload));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function updateTransaction(string $orderId, string $transactionId, array $payload): OrderTransactionUpdateResponseDTO
    {
        return OrderTransactionUpdateResponseDTO::fromArray($this->makeRequest(
            HttpMethod::PUT,
            '/v1/orders/'.rawurlencode($orderId).'/transactions/'.rawurlencode($transactionId),
            body: $payload,
        ));
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
