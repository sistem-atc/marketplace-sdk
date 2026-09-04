<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Vendor Direct Fulfillment Payments API v1 — envio de faturas do fornecedor
 * pra Amazon referentes a pedidos de Direct Fulfillment.
 *
 * ⚠️ API de **Vendor Central** (conta de fornecedor 1P, programa Direct
 * Fulfillment); NÃO se aplica a conta de seller (3P). Pra POs normais use
 * VendorInvoices.
 *
 * Path base: /vendor/directFulfillment/payments/v1. Rate limit do modelo:
 * 10 req/s, burst 10.
 */
class VendorDirectFulfillmentPayments
{
    private const BASE = '/vendor/directFulfillment/payments/v1';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Envia uma ou mais faturas. POST /invoices. Resposta: `payload.transactionId`.
     *
     * @param  array<string, mixed>  $body  SubmitInvoiceRequest {invoices: [...]}
     * @return array<string, mixed>
     */
    public function submitInvoice(array $body): array
    {
        return $this->client->post(self::BASE.'/invoices', $body);
    }
}
