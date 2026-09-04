<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Vendor Orders API v1 — ordens de compra (PO) que a Amazon emite pro
 * fornecedor.
 *
 * ⚠️ API de **Vendor Central** (conta de fornecedor 1P — a Amazon compra e
 * revende); NÃO se aplica a conta de seller (3P). Exige integração autorizada
 * num vendor group.
 *
 * Path base: /vendor/orders/v1. Respostas v1 embrulhadas em `payload`
 * (`payload.purchaseOrders`, `payload.pagination.nextToken`). Rate limit do
 * modelo: 10 req/s, burst 10 em todas as operações.
 */
class VendorOrders
{
    private const BASE = '/vendor/orders/v1';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Lista POs. GET /purchaseOrders. Paginação por `payload.pagination.nextToken`
     * → `$query['nextToken']`. Dado em `payload.purchaseOrders`.
     *
     * @param  array<string, mixed>  $query  limit, createdAfter, createdBefore, sortOrder,
     *   nextToken, includeDetails, changedAfter, changedBefore, poItemState, isPOChanged,
     *   purchaseOrderState, orderingVendorCode
     * @return array<string, mixed>
     */
    public function getPurchaseOrders(array $query = []): array
    {
        return $this->client->get(self::BASE.'/purchaseOrders', $query);
    }

    /**
     * Detalhe de uma PO. GET /purchaseOrders/{purchaseOrderNumber}. Dado em `payload`.
     *
     * @return array<string, mixed>
     */
    public function getPurchaseOrder(string $purchaseOrderNumber): array
    {
        return $this->client->get(self::BASE.'/purchaseOrders/'.rawurlencode($purchaseOrderNumber));
    }

    /**
     * Confirma (aceita/rejeita itens de) uma ou mais POs. POST /acknowledgements.
     * Resposta: `payload.transactionId` (consultar via VendorTransactionStatus).
     *
     * @param  array<string, mixed>  $body  SubmitAcknowledgementRequest {acknowledgements: [...]}
     * @return array<string, mixed>
     */
    public function submitAcknowledgement(array $body): array
    {
        return $this->client->post(self::BASE.'/acknowledgements', $body);
    }

    /**
     * Status de POs (confirmação/recebimento por item). GET /purchaseOrdersStatus.
     * Paginação `payload.pagination.nextToken`. Dado em `payload.ordersStatus`.
     *
     * @param  array<string, mixed>  $query  limit, sortOrder, nextToken, createdAfter, createdBefore,
     *   updatedAfter, updatedBefore, purchaseOrderNumber, purchaseOrderStatus, itemConfirmationStatus,
     *   itemReceiveStatus, orderingVendorCode, shipToPartyId
     * @return array<string, mixed>
     */
    public function getPurchaseOrdersStatus(array $query = []): array
    {
        return $this->client->get(self::BASE.'/purchaseOrdersStatus', $query);
    }
}
