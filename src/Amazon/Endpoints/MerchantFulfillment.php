<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Merchant Fulfillment API v0 (path /mfn/v0) — Buy Shipping pra pedidos MFN:
 * cotação de serviços elegíveis, criação/cancelamento de remessa com etiqueta.
 *
 * Rate limits (modelo): getEligibleShipmentServices 6 req/s + burst 12;
 * createShipment 2/2; getShipment, cancelShipment e getAdditionalSellerInputs
 * 1/1.
 *
 * Respostas v0 vêm embrulhadas em `payload` (o array inteiro é devolvido).
 */
class MerchantFulfillment
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Serviços de envio elegíveis pra um pedido
     * (POST /mfn/v0/eligibleShippingServices). Retorno em
     * `payload.ShippingServiceList`.
     *
     * @param  array<string, mixed>  $body  GetEligibleShipmentServicesRequest (ShipmentRequestDetails, ShippingOfferingFilter)
     */
    public function getEligibleShipmentServices(array $body): array
    {
        return $this->client->post('/mfn/v0/eligibleShippingServices', $body);
    }

    /**
     * Detalhe de uma remessa MFN (GET /mfn/v0/shipments/{shipmentId}).
     * Retorno em `payload` (ShipmentId, Status, TrackingId, Label).
     */
    public function getShipment(string $shipmentId): array
    {
        return $this->client->get('/mfn/v0/shipments/'.rawurlencode($shipmentId));
    }

    /**
     * Cancela uma remessa MFN (DELETE /mfn/v0/shipments/{shipmentId}).
     * Retorno em `payload` (a remessa com Status atualizado).
     */
    public function cancelShipment(string $shipmentId): array
    {
        return $this->client->delete('/mfn/v0/shipments/'.rawurlencode($shipmentId));
    }

    /**
     * Cria a remessa e compra a etiqueta (POST /mfn/v0/shipments). Retorno
     * em `payload` (ShipmentId, TrackingId, Label.FileContents).
     *
     * @param  array<string, mixed>  $body  CreateShipmentRequest (ShipmentRequestDetails, ShippingServiceId, ShipmentLevelSellerInputsList, ...)
     */
    public function createShipment(array $body): array
    {
        return $this->client->post('/mfn/v0/shipments', $body);
    }

    /**
     * Inputs adicionais exigidos por um serviço de envio
     * (POST /mfn/v0/additionalSellerInputs). Retorno em `payload`
     * (ShipmentLevelFields, ItemLevelFieldsList).
     *
     * @param  array<string, mixed>  $body  GetAdditionalSellerInputsRequest (ShippingServiceId, ShipFromAddress, OrderId)
     */
    public function getAdditionalSellerInputs(array $body): array
    {
        return $this->client->post('/mfn/v0/additionalSellerInputs', $body);
    }
}
