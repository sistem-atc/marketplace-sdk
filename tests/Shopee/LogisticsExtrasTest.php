<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Logistics\LogisticsMethods;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeRequestException;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function logisticsExtrasIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );
}

function logisticsExtrasClient(): LogisticsMethods
{
    $integration = logisticsExtrasIntegration();

    return new LogisticsMethods(HttpClientFactory::make($integration), $integration);
}

/** Query da URL como array. */
function logisticsExtrasQuery(string $url): array
{
    parse_str((string) parse_url($url, PHP_URL_QUERY), $q);

    return $q;
}

/** Verbo + path + assinatura de shop (partner_id, timestamp, sign, shop_id, access_token). */
function logisticsExtrasAssertShop(string $method, string $path): void
{
    Http::assertSent(function ($req) use ($method, $path) {
        $q = logisticsExtrasQuery($req->url());

        return $req->method() === $method
            && str_contains($req->url(), $path)
            && ($q['partner_id'] ?? null) === '2030136'
            && ($q['shop_id'] ?? null) === '999999'
            && ($q['access_token'] ?? null) === 'shopee-token'
            && isset($q['sign'], $q['timestamp']);
    });
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

$ok = fn (array $response = []) => Http::response(['error' => '', 'message' => '', 'request_id' => 'x', 'response' => $response]);

describe('LogisticsMethods extras — GET por pedido', function () use ($ok) {
    it('getShippingParameter manda order_sn e package_number', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/get_shipping_parameter*' => $ok(['info_needed' => ['pickup' => ['address_id']]])]);
        $r = logisticsExtrasClient()->getShippingParameter('ORD1', 'PKG1');
        expect($r['response']['info_needed']['pickup'])->toBe(['address_id']);
        logisticsExtrasAssertShop('GET', '/api/v2/logistics/get_shipping_parameter');
        Http::assertSent(fn ($req) => logisticsExtrasQuery($req->url())['order_sn'] === 'ORD1' && logisticsExtrasQuery($req->url())['package_number'] === 'PKG1');
    });

    it('getTrackingInfo omite package_number vazio', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/get_tracking_info*' => $ok(['tracking_info' => []])]);
        logisticsExtrasClient()->getTrackingInfo('ORD1', '');
        logisticsExtrasAssertShop('GET', '/api/v2/logistics/get_tracking_info');
        Http::assertSent(fn ($req) => ! array_key_exists('package_number', logisticsExtrasQuery($req->url())) && logisticsExtrasQuery($req->url())['order_sn'] === 'ORD1');
    });

    it('getTrackingNumber junta response_optional_fields por virgula', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/get_tracking_number*' => $ok(['tracking_number' => 'BR123'])]);
        $r = logisticsExtrasClient()->getTrackingNumber('ORD1', null, ['plp_number', 'first_mile_tracking_number']);
        expect($r['response']['tracking_number'])->toBe('BR123');
        logisticsExtrasAssertShop('GET', '/api/v2/logistics/get_tracking_number');
        Http::assertSent(fn ($req) => logisticsExtrasQuery($req->url())['response_optional_fields'] === 'plp_number,first_mile_tracking_number');
    });

    it('getBookingShippingParameter / getBookingTrackingNumber / getBookingTrackingInfo mandam booking_sn', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/get_booking_*' => $ok()]);
        $c = logisticsExtrasClient();
        $c->getBookingShippingParameter('BK1');
        $c->getBookingTrackingNumber('BK1');
        $c->getBookingTrackingInfo('BK1');
        foreach (['get_booking_shipping_parameter', 'get_booking_tracking_number', 'get_booking_tracking_info'] as $p) {
            logisticsExtrasAssertShop('GET', '/api/v2/logistics/'.$p);
        }
        Http::assertSentCount(3);
        Http::assertSent(fn ($req) => logisticsExtrasQuery($req->url())['booking_sn'] === 'BK1');
    });

    it('endpoints GET sem parametros: channel list, address list, operating hours, pause, mart packaging', function () use ($ok) {
        Http::fake(['*' => $ok()]);
        $c = logisticsExtrasClient();
        $c->getChannelList();
        $c->getAddressList();
        $c->getOperatingHourRestrictions();
        $c->getOperatingHours();
        $c->getPauseStatus();
        $c->getMartPackagingInfo();
        foreach (['get_channel_list', 'get_address_list', 'get_operating_hour_restrictions', 'get_operating_hours', 'get_pause_status', 'get_mart_packaging_info'] as $p) {
            logisticsExtrasAssertShop('GET', '/api/v2/logistics/'.$p);
        }
        Http::assertSentCount(6);
    });
});

describe('LogisticsMethods extras — envio', function () use ($ok) {
    it('shipOrder manda so os blocos informados', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/ship_order*' => $ok()]);
        logisticsExtrasClient()->shipOrder('ORD1', 'PKG1', pickup: ['address_id' => 5, 'pickup_time_id' => 't1']);
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/ship_order');
        Http::assertSent(function ($req) {
            $b = $req->data();

            return $b['order_sn'] === 'ORD1' && $b['package_number'] === 'PKG1'
                && $b['pickup'] === ['address_id' => 5, 'pickup_time_id' => 't1']
                && ! isset($b['dropoff'], $b['non_integrated']);
        });
    });

    it('batchShipOrder manda order_list + dropoff', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/batch_ship_order*' => $ok(['result_list' => []])]);
        logisticsExtrasClient()->batchShipOrder([['order_sn' => 'A'], ['order_sn' => 'B', 'package_number' => 'P']], dropoff: ['branch_id' => 1]);
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/batch_ship_order');
        Http::assertSent(fn ($req) => count($req->data()['order_list']) === 2 && $req->data()['dropoff'] === ['branch_id' => 1]);
    });

    it('updateShippingOrder manda pickup obrigatorio', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/update_shipping_order*' => $ok()]);
        logisticsExtrasClient()->updateShippingOrder('ORD1', ['address_id' => 5, 'pickup_time_id' => 't2']);
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/update_shipping_order');
        Http::assertSent(fn ($req) => $req->data()['pickup']['pickup_time_id'] === 't2' && ! isset($req->data()['package_number']));
    });

    it('updateTrackingStatus manda logistics_status e extras opcionais', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/update_tracking_status*' => $ok()]);
        logisticsExtrasClient()->updateTrackingStatus('ORD1', 'logistics_pickup_done', trackingNumber: 'TN1', trackingUrl: 'https://t/1');
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/update_tracking_status');
        Http::assertSent(fn ($req) => $req->data() === ['order_sn' => 'ORD1', 'logistics_status' => 'logistics_pickup_done', 'tracking_number' => 'TN1', 'tracking_url' => 'https://t/1']);
    });

    it('batchUpdateTpfWarehouseTrackingStatus manda tpf_name/status/package_list', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/batch_update_tpf_warehouse_tracking_status*' => $ok()]);
        logisticsExtrasClient()->batchUpdateTpfWarehouseTrackingStatus('WH', '3pf_warehouse_outbound_done', [['order_sn' => 'A', 'update_time' => 1]]);
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/batch_update_tpf_warehouse_tracking_status');
        Http::assertSent(fn ($req) => $req->data()['tpf_name'] === 'WH' && $req->data()['package_list'][0]['update_time'] === 1);
    });

    it('updateSelfCollectionOrderLogistics manda acao + imagens + pin', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/update_self_collection_order_logistics*' => $ok()]);
        logisticsExtrasClient()->updateSelfCollectionOrderLogistics('PKG1', 'order_collected', ['img1'], '1234');
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/update_self_collection_order_logistics');
        Http::assertSent(fn ($req) => $req->data() === ['package_number' => 'PKG1', 'self_collection_logistics_action' => 'order_collected', 'epoc_image_list' => ['img1'], 'pin' => '1234']);
    });

    it('shipBooking manda booking_sn + pickup', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/ship_booking*' => $ok()]);
        logisticsExtrasClient()->shipBooking('BK1', pickup: ['address_id' => 1]);
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/ship_booking');
        Http::assertSent(fn ($req) => $req->data() === ['booking_sn' => 'BK1', 'pickup' => ['address_id' => 1]]);
    });
});

describe('LogisticsMethods extras — mass ship', function () use ($ok) {
    it('getMassShippingParameter converte package numbers em package_list', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/get_mass_shipping_parameter*' => $ok()]);
        logisticsExtrasClient()->getMassShippingParameter(['P1', 'P2'], 90026, 'LOC1');
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/get_mass_shipping_parameter');
        Http::assertSent(fn ($req) => $req->data()['package_list'] === [['package_number' => 'P1'], ['package_number' => 'P2']]
            && $req->data()['logistics_channel_id'] === 90026 && $req->data()['product_location_id'] === 'LOC1');
    });

    it('massShipOrder manda package_list + non_integrated', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/mass_ship_order*' => $ok()]);
        logisticsExtrasClient()->massShipOrder(['P1'], nonIntegrated: ['tracking_number' => [['package_number' => 'P1', 'tracking_number' => 'T']]]);
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/mass_ship_order');
        Http::assertSent(fn ($req) => $req->data()['package_list'] === [['package_number' => 'P1']] && isset($req->data()['non_integrated']) && ! isset($req->data()['pickup']));
    });

    it('getMassTrackingNumber manda package_list + optional fields', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/get_mass_tracking_number*' => $ok(['success_list' => [], 'fail_list' => []])]);
        logisticsExtrasClient()->getMassTrackingNumber(['P1'], ['first_mile_tracking_number']);
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/get_mass_tracking_number');
        Http::assertSent(fn ($req) => $req->data()['response_optional_fields'] === 'first_mile_tracking_number');
    });
});

describe('LogisticsMethods extras — documentos de envio', function () use ($ok) {
    it('getShippingDocumentDataInfo manda recipient_address_info', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/get_shipping_document_data_info*' => $ok(['shipping_document_info' => ['cod' => false]])]);
        $r = logisticsExtrasClient()->getShippingDocumentDataInfo('ORD1', 'PKG1', [['key' => 'name', 'style' => ['font_size' => 12]]]);
        expect($r['response']['shipping_document_info']['cod'])->toBeFalse();
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/get_shipping_document_data_info');
        Http::assertSent(fn ($req) => $req->data()['recipient_address_info'][0]['key'] === 'name' && $req->data()['package_number'] === 'PKG1');
    });

    it('createShippingDocumentJob + getShippingDocumentJobStatus', function () use ($ok) {
        Http::fake([
            '*/api/v2/logistics/create_shipping_document_job*' => $ok(['job_id' => 'J1']),
            '*/api/v2/logistics/get_shipping_document_job_status*' => $ok(['job_id' => 'J1', 'job_status' => 'READY']),
        ]);
        $c = logisticsExtrasClient();
        $r = $c->createShippingDocumentJob(packageList: ['P1', 'P2']);
        expect($r['response']['job_id'])->toBe('J1');
        $s = $c->getShippingDocumentJobStatus('J1');
        expect($s['response']['job_status'])->toBe('READY');
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/create_shipping_document_job');
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/get_shipping_document_job_status');
        Http::assertSent(fn ($req) => str_contains($req->url(), 'create_shipping_document_job')
            ? $req->data() === ['shipping_document_type' => 'THERMAL_UNPACKAGED_LABEL', 'package_list' => ['P1', 'P2']]
            : $req->data() === ['job_id' => 'J1']);
    });

    it('downloadShippingDocumentJob devolve o binario cru', function () {
        Http::fake(['*/api/v2/logistics/download_shipping_document_job*' => Http::response('%PDF-1.4 job', 200, ['Content-Type' => 'application/pdf'])]);
        expect(logisticsExtrasClient()->downloadShippingDocumentJob('J1'))->toBe('%PDF-1.4 job');
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/download_shipping_document_job');
        Http::assertSent(fn ($req) => $req->data() === ['job_id' => 'J1']);
    });

    it('download binario com erro de negocio (JSON + HTTP 200) lanca ShopeeRequestException', function () {
        Http::fake(['*/api/v2/logistics/download_to_label*' => Http::response(['error' => 'error_param', 'message' => 'bad'], 200)]);
        expect(fn () => logisticsExtrasClient()->downloadToLabel(1))->toThrow(ShopeeRequestException::class);
    });

    it('downloadToLabel limita quantity a 20 e manda sorting_group', function () {
        Http::fake(['*/api/v2/logistics/download_to_label*' => Http::response('%PDF', 200, ['Content-Type' => 'application/pdf'])]);
        expect(logisticsExtrasClient()->downloadToLabel(2, 99))->toBe('%PDF');
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/download_to_label');
        Http::assertSent(fn ($req) => $req->data() === ['sorting_group' => 2, 'quantity' => 20]);
    });

    it('booking shipping document: parameter, create (default type), result, download, data info', function () use ($ok) {
        Http::fake([
            '*/api/v2/logistics/download_booking_shipping_document*' => Http::response('%PDF-bk', 200, ['Content-Type' => 'application/pdf']),
            '*/api/v2/logistics/*booking_shipping_document*' => $ok(['result_list' => []]),
        ]);
        $c = logisticsExtrasClient();
        $c->getBookingShippingDocumentParameter(['BK1', 'BK2']);
        $c->createBookingShippingDocument([['booking_sn' => 'BK1', 'tracking_number' => 'T1'], ['booking_sn' => 'BK2', 'shipping_document_type' => 'THERMAL_AIR_WAYBILL']]);
        $c->getBookingShippingDocumentResult([['booking_sn' => 'BK1', 'shipping_document_type' => 'NORMAL_AIR_WAYBILL']]);
        expect($c->downloadBookingShippingDocument(['BK1']))->toBe('%PDF-bk');
        $c->getBookingShippingDocumentDataInfo('BK1', [['key' => 'phone']]);

        foreach (['get_booking_shipping_document_parameter', 'create_booking_shipping_document', 'get_booking_shipping_document_result', 'download_booking_shipping_document', 'get_booking_shipping_document_data_info'] as $p) {
            logisticsExtrasAssertShop('POST', '/api/v2/logistics/'.$p);
        }
        Http::assertSent(fn ($req) => ! str_contains($req->url(), 'get_booking_shipping_document_parameter')
            || $req->data()['booking_list'] === [['booking_sn' => 'BK1'], ['booking_sn' => 'BK2']]);
        Http::assertSent(fn ($req) => ! str_contains($req->url(), 'create_booking_shipping_document')
            || ($req->data()['booking_list'][0]['shipping_document_type'] === 'NORMAL_AIR_WAYBILL' && $req->data()['booking_list'][1]['shipping_document_type'] === 'THERMAL_AIR_WAYBILL'));
        Http::assertSent(fn ($req) => ! str_contains($req->url(), 'download_booking_shipping_document')
            || $req->data() === ['shipping_document_type' => 'NORMAL_AIR_WAYBILL', 'booking_list' => [['booking_sn' => 'BK1']]]);
        Http::assertSent(fn ($req) => ! str_contains($req->url(), 'get_booking_shipping_document_data_info')
            || $req->data() === ['booking_sn' => 'BK1', 'recipient_address_info' => [['key' => 'phone']]]);
        Http::assertSentCount(5);
    });
});

describe('LogisticsMethods extras — canais e enderecos', function () use ($ok) {
    it('updateChannel manda so flags informadas', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/update_channel*' => $ok()]);
        logisticsExtrasClient()->updateChannel(90026, enabled: true, autoCallDriverSetting: ['auto_call_driver_enabled' => false]);
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/update_channel');
        Http::assertSent(fn ($req) => $req->data() === ['logistics_channel_id' => 90026, 'enabled' => true, 'auto_call_driver_setting' => ['auto_call_driver_enabled' => false]]);
    });

    it('updateAddress mescla address_id com os campos', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/update_address*' => $ok()]);
        logisticsExtrasClient()->updateAddress(11, ['city' => 'Sao Paulo', 'zipcode' => '01000-000']);
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/update_address');
        Http::assertSent(fn ($req) => $req->data() === ['address_id' => 11, 'city' => 'Sao Paulo', 'zipcode' => '01000-000']);
    });

    it('deleteAddress manda address_id', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/delete_address*' => $ok()]);
        logisticsExtrasClient()->deleteAddress(11);
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/delete_address');
        Http::assertSent(fn ($req) => $req->data() === ['address_id' => 11]);
    });

    it('setAddressConfig manda show_pickup_address + address_type_config', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/set_address_config*' => $ok()]);
        logisticsExtrasClient()->setAddressConfig(true, ['address_id' => 11, 'address_type' => ['PICKUP_ADDRESS']]);
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/set_address_config');
        Http::assertSent(fn ($req) => $req->data()['show_pickup_address'] === true && $req->data()['address_type_config']['address_type'] === ['PICKUP_ADDRESS']);
    });
});

describe('LogisticsMethods extras — horarios, pausa, embalagem, poligono', function () use ($ok) {
    it('updateOperatingHours envia apenas os blocos informados', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/update_operating_hours*' => $ok()]);
        logisticsExtrasClient()->updateOperatingHours(regularOperatingHour: ['monday' => ['start_time' => '09:00', 'end_time' => '18:00']]);
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/update_operating_hours');
        Http::assertSent(fn ($req) => array_keys($req->data()) === ['regular_operating_hour']);
    });

    it('deleteSpecialOperatingHour manda name', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/delete_special_operating_hour*' => $ok()]);
        logisticsExtrasClient()->deleteSpecialOperatingHour('Natal');
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/delete_special_operating_hour');
        Http::assertSent(fn ($req) => $req->data() === ['name' => 'Natal']);
    });

    it('setPauseStatus manda is_paused', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/set_pause_status*' => $ok()]);
        logisticsExtrasClient()->setPauseStatus(true);
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/set_pause_status');
        Http::assertSent(fn ($req) => $req->data() === ['is_paused' => true]);
    });

    it('setMartPackagingInfo manda enable + dimension + packaging_fee', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/set_mart_packaging_info*' => $ok()]);
        logisticsExtrasClient()->setMartPackagingInfo(true, ['length' => 1, 'width' => 2, 'height' => 3], ['value' => 1.5]);
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/set_mart_packaging_info');
        Http::assertSent(fn ($req) => $req->data()['enable'] === true && $req->data()['packaging_fee'] === ['value' => 1.5]);
    });

    it('uploadServiceablePolygon sobe multipart com campo file', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/upload_serviceable_polygon*' => $ok(['task_id' => 'T1'])]);
        $r = logisticsExtrasClient()->uploadServiceablePolygon('<kml/>', 'area.kml');
        expect($r['response']['task_id'])->toBe('T1');
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/upload_serviceable_polygon');
        Http::assertSent(fn ($req) => $req->isMultipart() && $req->hasFile('file', '<kml/>', 'area.kml'));
    });

    it('checkPolygonUpdateStatus manda task_id', function () use ($ok) {
        Http::fake(['*/api/v2/logistics/check_polygon_update_status*' => $ok(['status' => 'DONE'])]);
        logisticsExtrasClient()->checkPolygonUpdateStatus('T1');
        logisticsExtrasAssertShop('POST', '/api/v2/logistics/check_polygon_update_status');
        Http::assertSent(fn ($req) => $req->data() === ['task_id' => 'T1']);
    });
});
