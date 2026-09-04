<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Vendor Invoices API v1 — envio de faturas (invoice/credit note) do
 * fornecedor pra Amazon referentes a POs.
 *
 * ⚠️ API de **Vendor Central** (conta de fornecedor 1P); NÃO se aplica a
 * conta de seller (3P).
 *
 * Path base: /vendor/payments/v1. Rate limit do modelo: 10 req/s, burst 10.
 */
class VendorInvoices
{
    private const BASE = '/vendor/payments/v1';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Envia uma ou mais faturas. POST /invoices. Resposta: `payload.transactionId`
     * (acompanhar via VendorTransactionStatus::getTransaction).
     *
     * @param  array<string, mixed>  $body  SubmitInvoicesRequest {invoices: [...]}
     * @return array<string, mixed>
     */
    public function submitInvoices(array $body): array
    {
        return $this->client->post(self::BASE.'/invoices', $body);
    }
}
