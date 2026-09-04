<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Vendor Direct Fulfillment Transactions API (v1) — status das
 * transações assíncronas do Direct Fulfillment (acknowledgements, labels,
 * confirmações, invoices).
 *
 * ⚠️ API de **Vendor Central** (conta de fornecedor 1P, programa Direct
 * Fulfillment); NÃO se aplica a conta de seller (3P).
 *
 * Path base: /vendor/directFulfillment/transactions/v1. Dado em `payload.transactionStatus`.
 * Rate limit do modelo: 10 req/s, burst 10.
 * @deprecated Use VendorDirectFulfillmentTransactions (2021-12-28).
 */
class VendorDirectFulfillmentTransactionsV1
{
    private const BASE = '/vendor/directFulfillment/transactions/v1';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Status de uma transação. GET /transactions/{transactionId}.
     *
     * @return array<string, mixed>
     */
    public function getTransactionStatus(string $transactionId): array
    {
        return $this->client->get(self::BASE.'/transactions/'.rawurlencode($transactionId));
    }
}
