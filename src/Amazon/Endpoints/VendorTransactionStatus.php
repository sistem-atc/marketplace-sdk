<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Vendor Transaction Status API v1 — status das transações assíncronas das
 * APIs de vendor (acknowledgements, shipments, invoices…).
 *
 * ⚠️ API de **Vendor Central** (conta de fornecedor 1P); NÃO se aplica a
 * conta de seller (3P). Pra Direct Fulfillment use
 * VendorDirectFulfillmentTransactions.
 *
 * Path base: /vendor/transactions/v1. Rate limit do modelo: 10 req/s, burst 20.
 */
class VendorTransactionStatus
{
    private const BASE = '/vendor/transactions/v1';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Status de uma transação. GET /transactions/{transactionId}. Dado em
     * `payload.transactionStatus` (status Failure|Processing|Success + errors).
     *
     * @return array<string, mixed>
     */
    public function getTransaction(string $transactionId): array
    {
        return $this->client->get(self::BASE.'/transactions/'.rawurlencode($transactionId));
    }
}
