<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Vendor Direct Fulfillment Sandbox Test Data API (2021-10-28) — gera cenários
 * de pedido de teste no sandbox do Direct Fulfillment.
 *
 * ⚠️ API de **Vendor Central** (conta de fornecedor 1P, programa Direct
 * Fulfillment); NÃO se aplica a conta de seller (3P). Só faz sentido contra o
 * host sandbox (sandbox.sellingpartnerapi-*.amazon.com).
 *
 * Path base: /vendor/directFulfillment/sandbox/2021-10-28. Sem rate limit
 * declarado no modelo.
 */
class VendorDirectFulfillmentSandbox
{
    private const BASE = '/vendor/directFulfillment/sandbox/2021-10-28';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Gera cenários de pedido de teste. POST /orders. Resposta: `transactionId`.
     *
     * @param  array<string, mixed>  $body  GenerateOrderScenarioRequest {orders: [{sellingParty, shipFromParty}]}
     * @return array<string, mixed>
     */
    public function generateOrderScenarios(array $body): array
    {
        return $this->client->post(self::BASE.'/orders', $body);
    }

    /**
     * Status/resultado da geração de cenários. GET /transactions/{transactionId}.
     * Dado em `transactionStatus` (+ `testCaseData` quando SUCCESS).
     *
     * @return array<string, mixed>
     */
    public function getOrderScenarios(string $transactionId): array
    {
        return $this->client->get(self::BASE.'/transactions/'.rawurlencode($transactionId));
    }
}
