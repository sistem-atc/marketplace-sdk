<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\FulfillmentInbound;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function fulfillmentInboundIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );
}

function fulfillmentInbound(): FulfillmentInbound
{
    return new FulfillmentInbound(new Client(fulfillmentInboundIntegration()));
}

const FI_BASE = 'https://sellingpartnerapi-na.amazon.com/inbound/fba/2024-03-20';
const FI_PLAN = FI_BASE.'/inboundPlans/wf1234';
const FI_SHIP = FI_PLAN.'/shipments/sh5678';

/**
 * Executa a chamada e valida verbo + URL completa + token; `$body` (opcional)
 * confere chaves do JSON enviado.
 *
 * @param  array<string, mixed>  $body
 */
function fulfillmentInboundExpect(callable $call, string $method, string $url, array $body = []): void
{
    Http::fake(['*' => Http::response(['operationId' => 'op-1'])]);

    $call(fulfillmentInbound());

    Http::assertSent(function (Request $r) use ($method, $url, $body): bool {
        if ($r->method() !== $method || $r->url() !== $url || ! $r->hasHeader('x-amz-access-token', 'Atza|valid-token')) {
            return false;
        }
        foreach ($body as $k => $v) {
            if ($r[$k] !== $v) {
                return false;
            }
        }

        return true;
    });
}

// ── Inbound plans ────────────────────────────────────────────────────────────

it('listInboundPlans GET with pagination + status', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listInboundPlans(['pageSize' => 10, 'status' => 'ACTIVE', 'paginationToken' => 'pt']),
    'GET', FI_BASE.'/inboundPlans?pageSize=10&status=ACTIVE&paginationToken=pt',
));

it('createInboundPlan POST body', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->createInboundPlan(['name' => 'Plano', 'destinationMarketplaces' => ['A2Q3Y263D00KWC']]),
    'POST', FI_BASE.'/inboundPlans', ['name' => 'Plano', 'destinationMarketplaces' => ['A2Q3Y263D00KWC']],
));

it('getInboundPlan GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->getInboundPlan('wf1234'),
    'GET', FI_PLAN,
));

it('listInboundPlanBoxes GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listInboundPlanBoxes('wf1234', ['pageSize' => 5]),
    'GET', FI_PLAN.'/boxes?pageSize=5',
));

it('cancelInboundPlan PUT', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->cancelInboundPlan('wf1234'),
    'PUT', FI_PLAN.'/cancellation',
));

it('listInboundPlanItems GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listInboundPlanItems('wf1234', ['paginationToken' => 'pt']),
    'GET', FI_PLAN.'/items?paginationToken=pt',
));

it('updateInboundPlanName PUT body name', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->updateInboundPlanName('wf1234', 'Novo nome'),
    'PUT', FI_PLAN.'/name', ['name' => 'Novo nome'],
));

it('listInboundPlanPallets GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listInboundPlanPallets('wf1234'),
    'GET', FI_PLAN.'/pallets',
));

// ── Packing ──────────────────────────────────────────────────────────────────

it('listPackingGroupBoxes GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listPackingGroupBoxes('wf1234', 'pg1', ['pageSize' => 5]),
    'GET', FI_PLAN.'/packingGroups/pg1/boxes?pageSize=5',
));

it('listPackingGroupItems GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listPackingGroupItems('wf1234', 'pg1'),
    'GET', FI_PLAN.'/packingGroups/pg1/items',
));

it('setPackingInformation POST body', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->setPackingInformation('wf1234', ['packageGroupings' => [['packingGroupId' => 'pg1']]]),
    'POST', FI_PLAN.'/packingInformation', ['packageGroupings' => [['packingGroupId' => 'pg1']]],
));

it('listPackingOptions GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listPackingOptions('wf1234'),
    'GET', FI_PLAN.'/packingOptions',
));

it('generatePackingOptions POST', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->generatePackingOptions('wf1234'),
    'POST', FI_PLAN.'/packingOptions',
));

it('confirmPackingOption POST', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->confirmPackingOption('wf1234', 'po1'),
    'POST', FI_PLAN.'/packingOptions/po1/confirmation',
));

// ── Placement ────────────────────────────────────────────────────────────────

it('listPlacementOptions GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listPlacementOptions('wf1234', ['pageSize' => 20]),
    'GET', FI_PLAN.'/placementOptions?pageSize=20',
));

it('generatePlacementOptions POST body', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->generatePlacementOptions('wf1234', ['customPlacement' => []]),
    'POST', FI_PLAN.'/placementOptions', ['customPlacement' => []],
));

it('confirmPlacementOption POST', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->confirmPlacementOption('wf1234', 'pl1'),
    'POST', FI_PLAN.'/placementOptions/pl1/confirmation',
));

// ── Shipments ────────────────────────────────────────────────────────────────

it('getShipment GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->getShipment('wf1234', 'sh5678'),
    'GET', FI_SHIP,
));

it('listShipmentBoxes GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listShipmentBoxes('wf1234', 'sh5678', ['pageSize' => 5]),
    'GET', FI_SHIP.'/boxes?pageSize=5',
));

it('listShipmentContentUpdatePreviews GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listShipmentContentUpdatePreviews('wf1234', 'sh5678'),
    'GET', FI_SHIP.'/contentUpdatePreviews',
));

it('generateShipmentContentUpdatePreviews POST body', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->generateShipmentContentUpdatePreviews('wf1234', 'sh5678', ['boxes' => []]),
    'POST', FI_SHIP.'/contentUpdatePreviews', ['boxes' => []],
));

it('getShipmentContentUpdatePreview GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->getShipmentContentUpdatePreview('wf1234', 'sh5678', 'cu1'),
    'GET', FI_SHIP.'/contentUpdatePreviews/cu1',
));

it('confirmShipmentContentUpdatePreview POST', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->confirmShipmentContentUpdatePreview('wf1234', 'sh5678', 'cu1'),
    'POST', FI_SHIP.'/contentUpdatePreviews/cu1/confirmation',
));

it('getDeliveryChallanDocument GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->getDeliveryChallanDocument('wf1234', 'sh5678'),
    'GET', FI_SHIP.'/deliveryChallanDocument',
));

it('listDeliveryWindowOptions GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listDeliveryWindowOptions('wf1234', 'sh5678'),
    'GET', FI_SHIP.'/deliveryWindowOptions',
));

it('generateDeliveryWindowOptions POST', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->generateDeliveryWindowOptions('wf1234', 'sh5678'),
    'POST', FI_SHIP.'/deliveryWindowOptions',
));

it('confirmDeliveryWindowOptions POST', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->confirmDeliveryWindowOptions('wf1234', 'sh5678', 'dw1'),
    'POST', FI_SHIP.'/deliveryWindowOptions/dw1/confirmation',
));

it('listShipmentItems GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listShipmentItems('wf1234', 'sh5678', ['paginationToken' => 'pt']),
    'GET', FI_SHIP.'/items?paginationToken=pt',
));

it('updateShipmentName PUT body name', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->updateShipmentName('wf1234', 'sh5678', 'Envio 1'),
    'PUT', FI_SHIP.'/name', ['name' => 'Envio 1'],
));

it('listShipmentPallets GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listShipmentPallets('wf1234', 'sh5678'),
    'GET', FI_SHIP.'/pallets',
));

it('cancelSelfShipAppointment PUT body', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->cancelSelfShipAppointment('wf1234', 'sh5678', ['reasonComment' => 'OTHER']),
    'PUT', FI_SHIP.'/selfShipAppointmentCancellation', ['reasonComment' => 'OTHER'],
));

it('getSelfShipAppointmentSlots GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->getSelfShipAppointmentSlots('wf1234', 'sh5678', ['pageSize' => 5]),
    'GET', FI_SHIP.'/selfShipAppointmentSlots?pageSize=5',
));

it('generateSelfShipAppointmentSlots POST body', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->generateSelfShipAppointmentSlots('wf1234', 'sh5678', ['desiredStartDate' => '2026-09-10T00:00:00Z']),
    'POST', FI_SHIP.'/selfShipAppointmentSlots', ['desiredStartDate' => '2026-09-10T00:00:00Z'],
));

it('scheduleSelfShipAppointment POST body', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->scheduleSelfShipAppointment('wf1234', 'sh5678', 'slot1', ['reasonComment' => 'OTHER']),
    'POST', FI_SHIP.'/selfShipAppointmentSlots/slot1/schedule', ['reasonComment' => 'OTHER'],
));

it('updateShipmentSourceAddress PUT body', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->updateShipmentSourceAddress('wf1234', 'sh5678', ['address' => ['city' => 'Sao Paulo']]),
    'PUT', FI_SHIP.'/sourceAddress', ['address' => ['city' => 'Sao Paulo']],
));

it('updateShipmentTrackingDetails PUT body', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->updateShipmentTrackingDetails('wf1234', 'sh5678', ['trackingDetails' => ['ltlTrackingDetail' => ['billOfLadingNumber' => 'BOL1']]]),
    'PUT', FI_SHIP.'/trackingDetails', ['trackingDetails' => ['ltlTrackingDetail' => ['billOfLadingNumber' => 'BOL1']]],
));

// ── Transportation ───────────────────────────────────────────────────────────

it('listTransportationOptions GET with filters', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listTransportationOptions('wf1234', ['placementOptionId' => 'pl1', 'shipmentId' => 'sh5678']),
    'GET', FI_PLAN.'/transportationOptions?placementOptionId=pl1&shipmentId=sh5678',
));

it('generateTransportationOptions POST body', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->generateTransportationOptions('wf1234', ['placementOptionId' => 'pl1', 'shipmentTransportationConfigurations' => []]),
    'POST', FI_PLAN.'/transportationOptions', ['placementOptionId' => 'pl1'],
));

it('confirmTransportationOptions POST body', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->confirmTransportationOptions('wf1234', ['transportationSelections' => [['shipmentId' => 'sh5678', 'transportationOptionId' => 'to1']]]),
    'POST', FI_PLAN.'/transportationOptions/confirmation', ['transportationSelections' => [['shipmentId' => 'sh5678', 'transportationOptionId' => 'to1']]],
));

// ── Items ────────────────────────────────────────────────────────────────────

it('listItemComplianceDetails GET joins mskus', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listItemComplianceDetails(['SKU-A', 'SKU-B'], 'A2Q3Y263D00KWC'),
    'GET', FI_BASE.'/items/compliance?mskus=SKU-A%2CSKU-B&marketplaceId=A2Q3Y263D00KWC',
));

it('updateItemComplianceDetails PUT with marketplaceId query + body', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->updateItemComplianceDetails('A2Q3Y263D00KWC', ['taxDetails' => ['hsnCode' => '1234']]),
    'PUT', FI_BASE.'/items/compliance?marketplaceId=A2Q3Y263D00KWC', ['taxDetails' => ['hsnCode' => '1234']],
));

it('createMarketplaceItemLabels POST body', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->createMarketplaceItemLabels(['marketplaceId' => 'A2Q3Y263D00KWC', 'labelType' => 'STANDARD_FORMAT']),
    'POST', FI_BASE.'/items/labels', ['labelType' => 'STANDARD_FORMAT'],
));

it('listPrepDetails GET joins mskus', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->listPrepDetails('A2Q3Y263D00KWC', ['SKU-A', 'SKU-B']),
    'GET', FI_BASE.'/items/prepDetails?marketplaceId=A2Q3Y263D00KWC&mskus=SKU-A%2CSKU-B',
));

it('setPrepDetails POST body', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->setPrepDetails(['marketplaceId' => 'A2Q3Y263D00KWC', 'mskuPrepDetails' => []]),
    'POST', FI_BASE.'/items/prepDetails', ['marketplaceId' => 'A2Q3Y263D00KWC'],
));

// ── Operations ───────────────────────────────────────────────────────────────

it('getInboundOperationStatus GET', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->getInboundOperationStatus('op-1'),
    'GET', FI_BASE.'/operations/op-1',
));

it('path ids are rawurlencoded', fn () => fulfillmentInboundExpect(
    fn (FulfillmentInbound $a) => $a->getShipment('wf 1/2', 'sh#3'),
    'GET', FI_BASE.'/inboundPlans/wf%201%2F2/shipments/sh%233',
));
