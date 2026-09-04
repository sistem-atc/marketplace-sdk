<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Shipping API v1 (path /shipping/v1). Cria/compra remessas e consulta
 * rastreio via Amazon Shipping (comprador Buy Shipping).
 *
 * Rate limit (modelo): 5 req/s + burst 15, exceto getTrackingInformation
 * (1 req/s + burst 1).
 *
 * Respostas v1 vêm embrulhadas em `payload` (o array inteiro é devolvido).
 *
 * @deprecated Substituída pela Shipping API v2 ({@see Shipping}). Mantida
 *   por compatibilidade com contas ainda na v1.
 */
class ShippingV1
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Cria uma remessa (POST /shipping/v1/shipments). Retorno em
     * `payload.shipmentId`.
     *
     * @param  array<string, mixed>  $body  CreateShipmentRequest (clientReferenceId, shipTo, shipFrom, containers)
     */
    public function createShipment(array $body): array
    {
        return $this->client->post('/shipping/v1/shipments', $body);
    }

    /**
     * Detalhe de uma remessa (GET /shipping/v1/shipments/{shipmentId}).
     * Retorno em `payload`.
     */
    public function getShipment(string $shipmentId): array
    {
        return $this->client->get('/shipping/v1/shipments/'.rawurlencode($shipmentId));
    }

    /**
     * Cancela uma remessa (POST /shipping/v1/shipments/{shipmentId}/cancel).
     */
    public function cancelShipment(string $shipmentId): array
    {
        return $this->client->post('/shipping/v1/shipments/'.rawurlencode($shipmentId).'/cancel');
    }

    /**
     * Compra etiquetas de uma remessa já criada
     * (POST /shipping/v1/shipments/{shipmentId}/purchaseLabels). Retorno em
     * `payload.labelResults`.
     *
     * @param  array<string, mixed>  $body  PurchaseLabelsRequest (rateId, labelSpecification)
     */
    public function purchaseLabels(string $shipmentId, array $body): array
    {
        return $this->client->post('/shipping/v1/shipments/'.rawurlencode($shipmentId).'/purchaseLabels', $body);
    }

    /**
     * Reobtém a etiqueta de um container
     * (POST /shipping/v1/shipments/{shipmentId}/containers/{trackingId}/label).
     * Retorno em `payload.label`.
     *
     * @param  array<string, mixed>  $body  RetrieveShippingLabelRequest (labelSpecification)
     */
    public function retrieveShippingLabel(string $shipmentId, string $trackingId, array $body): array
    {
        return $this->client->post(
            '/shipping/v1/shipments/'.rawurlencode($shipmentId).'/containers/'.rawurlencode($trackingId).'/label',
            $body,
        );
    }

    /**
     * Cria e compra a remessa numa chamada só (POST /shipping/v1/purchaseShipment).
     * Retorno em `payload` (shipmentId, serviceRate, labelResults).
     *
     * @param  array<string, mixed>  $body  PurchaseShipmentRequest
     */
    public function purchaseShipment(array $body): array
    {
        return $this->client->post('/shipping/v1/purchaseShipment', $body);
    }

    /**
     * Cotação de serviços disponíveis (POST /shipping/v1/rates). Retorno em
     * `payload.serviceRates`.
     *
     * @param  array<string, mixed>  $body  GetRatesRequest (shipTo, shipFrom, containerSpecifications, serviceTypes)
     */
    public function getRates(array $body): array
    {
        return $this->client->post('/shipping/v1/rates', $body);
    }

    /**
     * Conta Amazon Shipping do seller (GET /shipping/v1/account). Retorno em
     * `payload.accountId`.
     */
    public function getAccount(): array
    {
        return $this->client->get('/shipping/v1/account');
    }

    /**
     * Rastreio de um tracking id (GET /shipping/v1/tracking/{trackingId}).
     * Rate limit 1 req/s + burst 1. Retorno em `payload` (summary, eventHistory).
     */
    public function getTrackingInformation(string $trackingId): array
    {
        return $this->client->get('/shipping/v1/tracking/'.rawurlencode($trackingId));
    }
}
