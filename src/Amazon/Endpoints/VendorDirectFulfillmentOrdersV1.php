<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Vendor Direct Fulfillment Orders API (v1) — pedidos de
 * consumidor que a Amazon repassa pro fornecedor despachar direto (dropship).
 *
 * ⚠️ API de **Vendor Central** (conta de fornecedor 1P, programa Direct
 * Fulfillment); NÃO se aplica a conta de seller (3P).
 *
 * Path base: /vendor/directFulfillment/orders/v1.
 * Respostas embrulhadas em `payload`.
 * Rate limit do modelo: 10 req/s, burst 10.
 * @deprecated Use VendorDirectFulfillmentOrders (2021-12-28).
 */
class VendorDirectFulfillmentOrdersV1
{
    private const BASE = '/vendor/directFulfillment/orders/v1';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Lista POs de direct fulfillment. GET /purchaseOrders. createdAfter/createdBefore
     * obrigatórios (ISO 8601). Paginação por `nextToken`.
     *
     * @param  array<string, mixed>  $query  shipFromPartyId, status (NEW|SHIPPED|ACCEPTED|CANCELLED),
     *   limit, sortOrder, nextToken, includeDetails
     * @return array<string, mixed>
     */
    public function getOrders(string $createdAfter, string $createdBefore, array $query = []): array
    {
        return $this->client->get(self::BASE.'/purchaseOrders', [
            'createdAfter' => $createdAfter,
            'createdBefore' => $createdBefore,
        ] + $query);
    }

    /**
     * Detalhe de uma PO. GET /purchaseOrders/{purchaseOrderNumber}.
     *
     * @return array<string, mixed>
     */
    public function getOrder(string $purchaseOrderNumber): array
    {
        return $this->client->get(self::BASE.'/purchaseOrders/'.rawurlencode($purchaseOrderNumber));
    }

    /**
     * Confirma (aceita/rejeita) POs. POST /acknowledgements. Resposta traz
     * `transactionId` (consultar via VendorDirectFulfillmentTransactions).
     *
     * @param  array<string, mixed>  $body  SubmitAcknowledgementRequest {orderAcknowledgements: [...]}
     * @return array<string, mixed>
     */
    public function submitAcknowledgement(array $body): array
    {
        return $this->client->post(self::BASE.'/acknowledgements', $body);
    }
}
