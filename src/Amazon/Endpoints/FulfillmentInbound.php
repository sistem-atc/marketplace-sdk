<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Fulfillment Inbound API 2024-03-20 da SP-API (`/inbound/fba/2024-03-20`).
 *
 * Fluxo: createInboundPlan → generatePackingOptions/confirmPackingOption →
 * setPackingInformation → generatePlacementOptions/confirmPlacementOption →
 * generateTransportationOptions/confirmTransportationOptions → shipments.
 * Operacoes de escrita (create/generate/confirm/update/cancel) sao assincronas:
 * devolvem `operationId`, acompanhado por {@see getInboundOperationStatus}.
 * Listagens paginam por `paginationToken` (em `pagination.nextToken`).
 *
 * Rate limits (modelo): listagens 2 req/s + burst 6 ou 30; escritas 2 req/s + burst 2.
 * Sem `payload`: a resposta e o objeto direto.
 */
class FulfillmentInbound
{
    private const BASE = '/inbound/fba/2024-03-20';

    public function __construct(
        private readonly Client $client,
    ) {}

    private function plan(string $inboundPlanId): string
    {
        return self::BASE.'/inboundPlans/'.rawurlencode($inboundPlanId);
    }

    private function shipment(string $inboundPlanId, string $shipmentId): string
    {
        return $this->plan($inboundPlanId).'/shipments/'.rawurlencode($shipmentId);
    }

    // ── Inbound plans ────────────────────────────────────────────────────────

    /**
     * Lista planos de inbound (GET .../inboundPlans). Retorno em `inboundPlans`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken, status, sortBy, sortOrder
     */
    public function listInboundPlans(array $query = []): array
    {
        return $this->client->get(self::BASE.'/inboundPlans', $query);
    }

    /**
     * Cria plano de inbound (POST .../inboundPlans). Retorna `inboundPlanId` + `operationId`.
     *
     * @param  array<string, mixed>  $body  destinationMarketplaces, items, sourceAddress, name
     */
    public function createInboundPlan(array $body): array
    {
        return $this->client->post(self::BASE.'/inboundPlans', $body);
    }

    /** Detalhe do plano (GET .../inboundPlans/{inboundPlanId}). */
    public function getInboundPlan(string $inboundPlanId): array
    {
        return $this->client->get($this->plan($inboundPlanId));
    }

    /**
     * Caixas do plano (GET .../{inboundPlanId}/boxes). Retorno em `boxes`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken
     */
    public function listInboundPlanBoxes(string $inboundPlanId, array $query = []): array
    {
        return $this->client->get($this->plan($inboundPlanId).'/boxes', $query);
    }

    /** Cancela o plano (PUT .../{inboundPlanId}/cancellation). Retorna `operationId`. */
    public function cancelInboundPlan(string $inboundPlanId): array
    {
        return $this->client->put($this->plan($inboundPlanId).'/cancellation');
    }

    /**
     * Itens do plano (GET .../{inboundPlanId}/items). Retorno em `items`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken
     */
    public function listInboundPlanItems(string $inboundPlanId, array $query = []): array
    {
        return $this->client->get($this->plan($inboundPlanId).'/items', $query);
    }

    /** Renomeia o plano (PUT .../{inboundPlanId}/name). Resposta 204 (vazia). */
    public function updateInboundPlanName(string $inboundPlanId, string $name): array
    {
        return $this->client->put($this->plan($inboundPlanId).'/name', ['name' => $name]);
    }

    /**
     * Paletes do plano (GET .../{inboundPlanId}/pallets). Retorno em `pallets`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken
     */
    public function listInboundPlanPallets(string $inboundPlanId, array $query = []): array
    {
        return $this->client->get($this->plan($inboundPlanId).'/pallets', $query);
    }

    // ── Packing ──────────────────────────────────────────────────────────────

    /**
     * Caixas de um packing group (GET .../packingGroups/{packingGroupId}/boxes). Retorno em `boxes`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken
     */
    public function listPackingGroupBoxes(string $inboundPlanId, string $packingGroupId, array $query = []): array
    {
        return $this->client->get($this->plan($inboundPlanId).'/packingGroups/'.rawurlencode($packingGroupId).'/boxes', $query);
    }

    /**
     * Itens de um packing group (GET .../packingGroups/{packingGroupId}/items). Retorno em `items`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken
     */
    public function listPackingGroupItems(string $inboundPlanId, string $packingGroupId, array $query = []): array
    {
        return $this->client->get($this->plan($inboundPlanId).'/packingGroups/'.rawurlencode($packingGroupId).'/items', $query);
    }

    /**
     * Informa como os itens foram embalados (POST .../{inboundPlanId}/packingInformation). Retorna `operationId`.
     *
     * @param  array<string, mixed>  $body  packageGroupings
     */
    public function setPackingInformation(string $inboundPlanId, array $body): array
    {
        return $this->client->post($this->plan($inboundPlanId).'/packingInformation', $body);
    }

    /**
     * Opcoes de embalagem geradas (GET .../{inboundPlanId}/packingOptions). Retorno em `packingOptions`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken
     */
    public function listPackingOptions(string $inboundPlanId, array $query = []): array
    {
        return $this->client->get($this->plan($inboundPlanId).'/packingOptions', $query);
    }

    /** Gera opcoes de embalagem (POST .../{inboundPlanId}/packingOptions). Retorna `operationId`. */
    public function generatePackingOptions(string $inboundPlanId): array
    {
        return $this->client->post($this->plan($inboundPlanId).'/packingOptions');
    }

    /** Confirma uma opcao de embalagem (POST .../packingOptions/{packingOptionId}/confirmation). Retorna `operationId`. */
    public function confirmPackingOption(string $inboundPlanId, string $packingOptionId): array
    {
        return $this->client->post($this->plan($inboundPlanId).'/packingOptions/'.rawurlencode($packingOptionId).'/confirmation');
    }

    // ── Placement ────────────────────────────────────────────────────────────

    /**
     * Opcoes de destino/placement (GET .../{inboundPlanId}/placementOptions). Retorno em `placementOptions`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken
     */
    public function listPlacementOptions(string $inboundPlanId, array $query = []): array
    {
        return $this->client->get($this->plan($inboundPlanId).'/placementOptions', $query);
    }

    /**
     * Gera opcoes de placement (POST .../{inboundPlanId}/placementOptions). Retorna `operationId`.
     *
     * @param  array<string, mixed>  $body  customPlacement (opcional)
     */
    public function generatePlacementOptions(string $inboundPlanId, array $body = []): array
    {
        return $this->client->post($this->plan($inboundPlanId).'/placementOptions', $body);
    }

    /** Confirma placement (POST .../placementOptions/{placementOptionId}/confirmation). Retorna `operationId`. */
    public function confirmPlacementOption(string $inboundPlanId, string $placementOptionId): array
    {
        return $this->client->post($this->plan($inboundPlanId).'/placementOptions/'.rawurlencode($placementOptionId).'/confirmation');
    }

    // ── Shipments ────────────────────────────────────────────────────────────

    /** Detalhe do envio (GET .../shipments/{shipmentId}). */
    public function getShipment(string $inboundPlanId, string $shipmentId): array
    {
        return $this->client->get($this->shipment($inboundPlanId, $shipmentId));
    }

    /**
     * Caixas do envio (GET .../shipments/{shipmentId}/boxes). Retorno em `boxes`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken
     */
    public function listShipmentBoxes(string $inboundPlanId, string $shipmentId, array $query = []): array
    {
        return $this->client->get($this->shipment($inboundPlanId, $shipmentId).'/boxes', $query);
    }

    /**
     * Previews de alteracao de conteudo (GET .../contentUpdatePreviews). Retorno em `contentUpdatePreviews`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken
     */
    public function listShipmentContentUpdatePreviews(string $inboundPlanId, string $shipmentId, array $query = []): array
    {
        return $this->client->get($this->shipment($inboundPlanId, $shipmentId).'/contentUpdatePreviews', $query);
    }

    /**
     * Gera preview de alteracao de conteudo do envio (POST .../contentUpdatePreviews). Retorna `operationId`.
     *
     * @param  array<string, mixed>  $body  boxes
     */
    public function generateShipmentContentUpdatePreviews(string $inboundPlanId, string $shipmentId, array $body): array
    {
        return $this->client->post($this->shipment($inboundPlanId, $shipmentId).'/contentUpdatePreviews', $body);
    }

    /** Detalhe de um preview (GET .../contentUpdatePreviews/{contentUpdatePreviewId}). */
    public function getShipmentContentUpdatePreview(string $inboundPlanId, string $shipmentId, string $contentUpdatePreviewId): array
    {
        return $this->client->get($this->shipment($inboundPlanId, $shipmentId).'/contentUpdatePreviews/'.rawurlencode($contentUpdatePreviewId));
    }

    /** Confirma um preview (POST .../contentUpdatePreviews/{id}/confirmation). Retorna `operationId`. */
    public function confirmShipmentContentUpdatePreview(string $inboundPlanId, string $shipmentId, string $contentUpdatePreviewId): array
    {
        return $this->client->post($this->shipment($inboundPlanId, $shipmentId).'/contentUpdatePreviews/'.rawurlencode($contentUpdatePreviewId).'/confirmation');
    }

    /** Delivery Challan (India) do envio (GET .../deliveryChallanDocument). Retorno em `documentDownload`. */
    public function getDeliveryChallanDocument(string $inboundPlanId, string $shipmentId): array
    {
        return $this->client->get($this->shipment($inboundPlanId, $shipmentId).'/deliveryChallanDocument');
    }

    /**
     * Janelas de entrega geradas (GET .../deliveryWindowOptions). Retorno em `deliveryWindowOptions`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken
     */
    public function listDeliveryWindowOptions(string $inboundPlanId, string $shipmentId, array $query = []): array
    {
        return $this->client->get($this->shipment($inboundPlanId, $shipmentId).'/deliveryWindowOptions', $query);
    }

    /** Gera janelas de entrega (POST .../deliveryWindowOptions). Retorna `operationId`. */
    public function generateDeliveryWindowOptions(string $inboundPlanId, string $shipmentId): array
    {
        return $this->client->post($this->shipment($inboundPlanId, $shipmentId).'/deliveryWindowOptions');
    }

    /** Confirma janela de entrega (POST .../deliveryWindowOptions/{deliveryWindowOptionId}/confirmation). Retorna `operationId`. */
    public function confirmDeliveryWindowOptions(string $inboundPlanId, string $shipmentId, string $deliveryWindowOptionId): array
    {
        return $this->client->post($this->shipment($inboundPlanId, $shipmentId).'/deliveryWindowOptions/'.rawurlencode($deliveryWindowOptionId).'/confirmation');
    }

    /**
     * Itens do envio (GET .../shipments/{shipmentId}/items). Retorno em `items`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken
     */
    public function listShipmentItems(string $inboundPlanId, string $shipmentId, array $query = []): array
    {
        return $this->client->get($this->shipment($inboundPlanId, $shipmentId).'/items', $query);
    }

    /** Renomeia o envio (PUT .../shipments/{shipmentId}/name). Resposta 204 (vazia). */
    public function updateShipmentName(string $inboundPlanId, string $shipmentId, string $name): array
    {
        return $this->client->put($this->shipment($inboundPlanId, $shipmentId).'/name', ['name' => $name]);
    }

    /**
     * Paletes do envio (GET .../shipments/{shipmentId}/pallets). Retorno em `pallets`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken
     */
    public function listShipmentPallets(string $inboundPlanId, string $shipmentId, array $query = []): array
    {
        return $this->client->get($this->shipment($inboundPlanId, $shipmentId).'/pallets', $query);
    }

    /**
     * Cancela agendamento self-ship (PUT .../selfShipAppointmentCancellation). Retorna `operationId`.
     *
     * @param  array<string, mixed>  $body  reasonComment (opcional)
     */
    public function cancelSelfShipAppointment(string $inboundPlanId, string $shipmentId, array $body = []): array
    {
        return $this->client->put($this->shipment($inboundPlanId, $shipmentId).'/selfShipAppointmentCancellation', $body);
    }

    /**
     * Slots de agendamento self-ship (GET .../selfShipAppointmentSlots). Retorno em `selfShipAppointmentSlotsAvailability`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken
     */
    public function getSelfShipAppointmentSlots(string $inboundPlanId, string $shipmentId, array $query = []): array
    {
        return $this->client->get($this->shipment($inboundPlanId, $shipmentId).'/selfShipAppointmentSlots', $query);
    }

    /**
     * Gera slots de agendamento self-ship (POST .../selfShipAppointmentSlots). Retorna `operationId`.
     *
     * @param  array<string, mixed>  $body  desiredStartDate, desiredEndDate
     */
    public function generateSelfShipAppointmentSlots(string $inboundPlanId, string $shipmentId, array $body = []): array
    {
        return $this->client->post($this->shipment($inboundPlanId, $shipmentId).'/selfShipAppointmentSlots', $body);
    }

    /**
     * Agenda self-ship num slot (POST .../selfShipAppointmentSlots/{slotId}/schedule). Retorno em `selfShipAppointmentDetails`.
     *
     * @param  array<string, mixed>  $body  reasonComment (opcional)
     */
    public function scheduleSelfShipAppointment(string $inboundPlanId, string $shipmentId, string $slotId, array $body = []): array
    {
        return $this->client->post($this->shipment($inboundPlanId, $shipmentId).'/selfShipAppointmentSlots/'.rawurlencode($slotId).'/schedule', $body);
    }

    /**
     * Atualiza endereco de origem do envio (PUT .../sourceAddress). Retorna `operationId`.
     *
     * @param  array<string, mixed>  $body  address
     */
    public function updateShipmentSourceAddress(string $inboundPlanId, string $shipmentId, array $body): array
    {
        return $this->client->put($this->shipment($inboundPlanId, $shipmentId).'/sourceAddress', $body);
    }

    /**
     * Atualiza rastreio do envio (PUT .../trackingDetails). Retorna `operationId`.
     *
     * @param  array<string, mixed>  $body  trackingDetails (spdTrackingDetail | ltlTrackingDetail)
     */
    public function updateShipmentTrackingDetails(string $inboundPlanId, string $shipmentId, array $body): array
    {
        return $this->client->put($this->shipment($inboundPlanId, $shipmentId).'/trackingDetails', $body);
    }

    // ── Transportation ───────────────────────────────────────────────────────

    /**
     * Opcoes de transporte (GET .../{inboundPlanId}/transportationOptions). Retorno em `transportationOptions`.
     *
     * @param  array<string, mixed>  $query  pageSize, paginationToken, placementOptionId, shipmentId
     */
    public function listTransportationOptions(string $inboundPlanId, array $query = []): array
    {
        return $this->client->get($this->plan($inboundPlanId).'/transportationOptions', $query);
    }

    /**
     * Gera opcoes de transporte (POST .../{inboundPlanId}/transportationOptions). Retorna `operationId`.
     *
     * @param  array<string, mixed>  $body  placementOptionId, shipmentTransportationConfigurations
     */
    public function generateTransportationOptions(string $inboundPlanId, array $body): array
    {
        return $this->client->post($this->plan($inboundPlanId).'/transportationOptions', $body);
    }

    /**
     * Confirma opcoes de transporte (POST .../transportationOptions/confirmation). Retorna `operationId`.
     *
     * @param  array<string, mixed>  $body  transportationSelections
     */
    public function confirmTransportationOptions(string $inboundPlanId, array $body): array
    {
        return $this->client->post($this->plan($inboundPlanId).'/transportationOptions/confirmation', $body);
    }

    // ── Items (compliance / labels / prep) ───────────────────────────────────

    /**
     * Compliance de itens por MSKU (GET .../items/compliance). Retorno em `complianceDetails`.
     *
     * @param  list<string>  $mskus  ate 100
     */
    public function listItemComplianceDetails(array $mskus, string $marketplaceId): array
    {
        return $this->client->get(self::BASE.'/items/compliance', ['mskus' => implode(',', $mskus), 'marketplaceId' => $marketplaceId]);
    }

    /**
     * Atualiza compliance de itens (PUT .../items/compliance?marketplaceId=). Retorna `operationId`.
     *
     * @param  array<string, mixed>  $body  taxDetails
     */
    public function updateItemComplianceDetails(string $marketplaceId, array $body): array
    {
        return $this->client->put(self::BASE.'/items/compliance?'.http_build_query(['marketplaceId' => $marketplaceId]), $body);
    }

    /**
     * Gera etiquetas de item (POST .../items/labels). Retorno em `documentDownloads`.
     *
     * @param  array<string, mixed>  $body  marketplaceId, labelType, mskuQuantities, height, width, pageType, locale
     */
    public function createMarketplaceItemLabels(array $body): array
    {
        return $this->client->post(self::BASE.'/items/labels', $body);
    }

    /**
     * Detalhes de preparo por MSKU (GET .../items/prepDetails). Retorno em `mskuPrepDetails`.
     *
     * @param  list<string>  $mskus  ate 100
     */
    public function listPrepDetails(string $marketplaceId, array $mskus): array
    {
        return $this->client->get(self::BASE.'/items/prepDetails', ['marketplaceId' => $marketplaceId, 'mskus' => implode(',', $mskus)]);
    }

    /**
     * Define preparo por MSKU (POST .../items/prepDetails). Retorna `operationId`.
     *
     * @param  array<string, mixed>  $body  marketplaceId, mskuPrepDetails
     */
    public function setPrepDetails(array $body): array
    {
        return $this->client->post(self::BASE.'/items/prepDetails', $body);
    }

    // ── Operations ───────────────────────────────────────────────────────────

    /** Status de uma operacao assincrona (GET .../operations/{operationId}): `operationStatus`, `operationProblems`. */
    public function getInboundOperationStatus(string $operationId): array
    {
        return $this->client->get(self::BASE.'/operations/'.rawurlencode($operationId));
    }
}
