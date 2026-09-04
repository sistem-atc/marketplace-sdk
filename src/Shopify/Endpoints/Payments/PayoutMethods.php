<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Payments;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Shopify Payments (Admin API REST — `payout`, `payment_transaction`) e
 * transacoes de pagamento do pedido no caixa (`tender_transaction`).
 * Exige escopo `read_shopify_payments_payouts` (payout/balance) e
 * `read_orders` (tender_transactions).
 */
class PayoutMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    /**
     * Lista repasses (payouts) do Shopify Payments.
     *
     * @param  array<string, mixed>  $params  ex.: since_id, last_id, date_min, date_max, date, status
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/shopify_payments/payouts', $params);
    }

    /**
     * Itera TODOS os repasses (paginacao por cursor).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function each(array $params = [], int $limit = 250): \Generator
    {
        return $this->eachPage('/shopify_payments/payouts', 'payouts', $params, $limit);
    }

    /**
     * Recupera um repasse.
     */
    public function get(int|string $payoutId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/shopify_payments/payouts/{$payoutId}");
    }

    /**
     * Transacoes do saldo do Shopify Payments (charges, refunds, adjustments,
     * payouts...) — `payment_transaction`.
     *
     * @param  array<string, mixed>  $params  ex.: since_id, last_id, test, payout_id, payout_status
     */
    public function balanceTransactions(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/shopify_payments/balance/transactions', $params);
    }

    /**
     * Itera TODAS as transacoes do saldo (paginacao por cursor).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function eachBalanceTransaction(array $params = [], int $limit = 250): \Generator
    {
        return $this->eachPage('/shopify_payments/balance/transactions', 'transactions', $params, $limit);
    }

    /**
     * Transacoes de pagamento dos pedidos (tender_transactions): cada
     * pagamento/estorno com gateway, valor e processed_at.
     *
     * @param  array<string, mixed>  $params  ex.: limit, since_id, processed_at_min, processed_at_max, processed_at, order
     */
    public function tenderTransactions(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/tender_transactions', $params);
    }

    /**
     * Itera TODAS as tender_transactions (paginacao por cursor).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function eachTenderTransaction(array $params = [], int $limit = 250): \Generator
    {
        return $this->eachPage('/tender_transactions', 'tender_transactions', $params, $limit);
    }
}
