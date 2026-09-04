<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Order\OrderItem;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Order\OrderListResponseDTO;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Order\OrderResponseDTO;

/**
 * Endpoint Orders v0 da SP-API (busca por AmazonOrderId individual).
 *
 * Rate limits Amazon:
 *   - GET /orders/{id}            : 2 req/s + burst 30
 *   - GET /orders/{id}/orderItems : 0.5 req/s + burst 30 (o gargalo)
 */
class Orders
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Lista de pedidos (GET /orders/v0/orders). Paginacao por NextToken.
     *
     * @param  array<string, mixed>  $query  MarketplaceIds, LastUpdatedAfter, NextToken, ...
     */
    public function getOrders(array $query): OrderListResponseDTO
    {
        $resp = $this->client->get('/orders/v0/orders', $query);

        return OrderListResponseDTO::fromArray((array) data_get($resp, 'payload', []));
    }

    /**
     * Header do pedido (GET /orders/v0/orders/{orderId}). Pedido inexistente
     * (404) vem com AmazonOrderId null.
     */
    public function getOrder(string $amazonOrderId): OrderResponseDTO
    {
        $resp = $this->client->get('/orders/v0/orders/'.rawurlencode($amazonOrderId));

        return OrderResponseDTO::fromArray((array) data_get($resp, 'payload', []));
    }

    /**
     * Itens do pedido (GET /orders/v0/orders/{orderId}/orderItems). Vazio em
     * 404 / sem itens. NAO pagina NextToken (pedidos BR raramente passam de 1
     * pagina).
     *
     * @return list<OrderItem>
     */
    public function getOrderItems(string $amazonOrderId): array
    {
        $resp = $this->client->get('/orders/v0/orders/'.rawurlencode($amazonOrderId).'/orderItems');

        return array_map(
            static fn (array $i): OrderItem => OrderItem::fromArray($i),
            data_get($resp, 'payload.OrderItems', []),
        );
    }

    // -------------------------------------------------------------------
    // Orders v0 — operações RESTRITAS (PII) e de expedição
    // -------------------------------------------------------------------

    /**
     * Dados do COMPRADOR (GET /orders/v0/orders/{orderId}/buyerInfo) — e-mail,
     * nome, CPF/CNPJ (BuyerTaxInfo), PurchaseOrderNumber. RESTRITA: o Client
     * pede um RDT pro path e usa no lugar do LWA. Dado em `payload`.
     * Rate limit: 0.5 req/s + burst 30.
     *
     * @return array<string, mixed>
     */
    public function getOrderBuyerInfo(string $amazonOrderId): array
    {
        return $this->client->getRestricted('/orders/v0/orders/'.rawurlencode($amazonOrderId).'/buyerInfo');
    }

    /**
     * ENDEREÇO de entrega (GET /orders/v0/orders/{orderId}/address). RESTRITA
     * (RDT automático). Dado em `payload.ShippingAddress`.
     * Rate limit: 0.5 req/s + burst 30.
     *
     * @return array<string, mixed>
     */
    public function getOrderAddress(string $amazonOrderId): array
    {
        return $this->client->getRestricted('/orders/v0/orders/'.rawurlencode($amazonOrderId).'/address');
    }

    /**
     * Dados do comprador POR ITEM (GET /orders/v0/orders/{orderId}/orderItems/buyerInfo)
     * — personalização/gift. RESTRITA (RDT). Dado em `payload.OrderItems`,
     * paginado por `payload.NextToken` (query NextToken).
     * Rate limit: 0.5 req/s + burst 30.
     *
     * @param  array<string, mixed>  $query  NextToken
     * @return array<string, mixed>
     */
    public function getOrderItemsBuyerInfo(string $amazonOrderId, array $query = []): array
    {
        return $this->client->getRestricted(
            '/orders/v0/orders/'.rawurlencode($amazonOrderId).'/orderItems/buyerInfo',
            $query,
        );
    }

    /**
     * Info regulatória do pedido (GET /orders/v0/orders/{orderId}/regulatedInfo)
     * — produtos que exigem verificação (farmácia etc.). RESTRITA (RDT).
     * Dado em `payload`. Rate limit: 0.5 req/s + burst 30.
     *
     * @return array<string, mixed>
     */
    public function getOrderRegulatedInfo(string $amazonOrderId): array
    {
        return $this->client->getRestricted('/orders/v0/orders/'.rawurlencode($amazonOrderId).'/regulatedInfo');
    }

    /**
     * Atualiza o status de verificação regulatória
     * (PATCH /orders/v0/orders/{orderId}/regulatedInfo). Body obrigatório:
     * `regulatedOrderVerificationStatus` {status: Approved|Rejected,
     * rejectionReasonId?, externalReviewerId?, verificationDetails?}.
     * Rate limit: 0.5 req/s + burst 30.
     *
     * @param  array<string, mixed>  $regulatedOrderVerificationStatus
     * @return array<string, mixed>
     */
    public function updateVerificationStatus(string $amazonOrderId, array $regulatedOrderVerificationStatus): array
    {
        return $this->client->patch(
            '/orders/v0/orders/'.rawurlencode($amazonOrderId).'/regulatedInfo',
            ['regulatedOrderVerificationStatus' => $regulatedOrderVerificationStatus],
        );
    }

    /**
     * Atualiza o status de ENVIO (POST /orders/v0/orders/{orderId}/shipment) —
     * só pra pedidos FBM com "Amazon Easy Ship"/entrega própria: shipmentStatus
     * ReadyForPickup | PickedUp | RefusedPickup. Body opcional `orderItems`
     * [{orderItemId, quantity}]. Rate limit: 5 req/s + burst 15.
     *
     * @param  array<string, mixed>  $body  extras (orderItems)
     * @return array<string, mixed>
     */
    public function updateShipmentStatus(string $amazonOrderId, string $marketplaceId, string $shipmentStatus, array $body = []): array
    {
        return $this->client->post(
            '/orders/v0/orders/'.rawurlencode($amazonOrderId).'/shipment',
            ['marketplaceId' => $marketplaceId, 'shipmentStatus' => $shipmentStatus] + $body,
        );
    }

    /**
     * CONFIRMA a expedição de um pedido FBM
     * (POST /orders/v0/orders/{orderId}/shipmentConfirmation) — informa
     * transportadora, rastreio e itens embarcados. `packageDetail` =
     * {packageReferenceId, carrierCode, carrierName?, shippingMethod?,
     * trackingNumber, shipDate, shipFromSupplySourceId?, orderItems[]}.
     * Body opcional: codCollectionMethod (DirectPayment — só JP).
     * Rate limit: 2 req/s + burst 10. Resposta 204 (vazia).
     *
     * @param  array<string, mixed>  $packageDetail
     * @param  array<string, mixed>  $body  extras (codCollectionMethod)
     * @return array<string, mixed>
     */
    public function confirmShipment(string $amazonOrderId, string $marketplaceId, array $packageDetail, array $body = []): array
    {
        return $this->client->post(
            '/orders/v0/orders/'.rawurlencode($amazonOrderId).'/shipmentConfirmation',
            ['marketplaceId' => $marketplaceId, 'packageDetail' => $packageDetail] + $body,
        );
    }
}
