<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Vendor Direct Fulfillment Transactions API (2021-12-28) — status das
 * transações assíncronas do Direct Fulfillment (acknowledgements, labels,
 * confirmações, invoices).
 *
 * ⚠️ API de **Vendor Central** (conta de fornecedor 1P, programa Direct
 * Fulfillment); NÃO se aplica a conta de seller (3P).
 *
 * Path base: /vendor/directFulfillment/transactions/2021-12-28. Dado em `transactionStatus` (sem envelope `payload`).
 * Rate limit do modelo: 10 req/s, burst 10.
 */
class VendorDirectFulfillmentTransactions
{
    private const BASE = '/vendor/directFulfillment/transactions/2021-12-28';

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
