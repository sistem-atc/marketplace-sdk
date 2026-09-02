<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Flex\FlexMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function flexTestIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function flexTestMethods(): FlexMethods
{
    $integration = flexTestIntegration();

    return new FlexMethods(HttpClientFactory::make($integration), $integration);
}

beforeEach(function () {
    config([
        'marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.access_token_ttl_seconds' => 21600,
        'mercadolivre.default_site_id' => 'MLB',
    ]);
    Http::preventStrayRequests();
});

const FLEX_SVC = 'https://api.mercadolibre.com/flex/sites/MLB/users/64196652/services/738216/configurations';

describe('FlexMethods', function () {
    it('subscriptions: GET /flex/sites/{site}/users/{user}/subscriptions/v1', function () {
        Http::fake(['api.mercadolibre.com/flex/sites/MLB/users/64196652/subscriptions/v1' => Http::response([['service_id' => 738216, 'mode' => 'FLEX']])]);

        expect(flexTestMethods()->subscriptions('MLB', 64196652)[0]['mode'])->toBe('FLEX');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/flex/sites/MLB/users/64196652/subscriptions/v1'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('itemStatus: GET /flex/sites/{site}/items/{item}/v2', function () {
        Http::fake(['api.mercadolibre.com/flex/sites/MLB/items/MLB1493119403/v2' => Http::response(['has_flex' => true])]);

        expect(flexTestMethods()->itemStatus('MLB', 'MLB1493119403')['has_flex'])->toBeTrue();
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/flex/sites/MLB/items/MLB1493119403/v2'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('enableItem: POST /flex/sites/{site}/items/{item}/v2 -> 204 vira []', function () {
        Http::fake(['api.mercadolibre.com/flex/sites/MLB/items/MLB1493119403/v2' => Http::response('', 204)]);

        expect(flexTestMethods()->enableItem('MLB', 'MLB1493119403'))->toBe([]);
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/flex/sites/MLB/items/MLB1493119403/v2'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('disableItem: DELETE /flex/sites/{site}/items/{item}/v2', function () {
        Http::fake(['api.mercadolibre.com/flex/sites/MLB/items/MLB1493119403/v2' => Http::response('', 204)]);

        flexTestMethods()->disableItem('MLB', 'MLB1493119403');
        Http::assertSent(fn ($req) => $req->method() === 'DELETE'
            && $req->url() === 'https://api.mercadolibre.com/flex/sites/MLB/items/MLB1493119403/v2'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('assignment: GET /flex/sites/{site}/shipments/{id}/assignment/v2', function () {
        Http::fake(['api.mercadolibre.com/flex/sites/MLA/shipments/40070866801/assignment/v2' => Http::response(['driver_id' => 1234])]);

        expect(flexTestMethods()->assignment('MLA', 40070866801)['driver_id'])->toBe(1234);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/flex/sites/MLA/shipments/40070866801/assignment/v2');
    });

    it('registerCourierShipment: POST courier-shipment/v1 com shipment_id', function () {
        Http::fake(['api.mercadolibre.com/flex/sites/MLA/users/1444885522/courier-shipment/v1' => Http::response('', 204)]);

        flexTestMethods()->registerCourierShipment('MLA', 1444885522, 123456786);
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/flex/sites/MLA/users/1444885522/courier-shipment/v1'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer')
            && $req['shipment_id'] === 123456786);
    });

    it('coverageZones: GET coverage/zones/v1?show_availables=true', function () {
        Http::fake([FLEX_SVC.'/coverage/zones/v1*' => Http::response(['zones' => [['id' => 'Sao_Paulo']]])]);

        expect(flexTestMethods()->coverageZones('MLB', 64196652, 738216, true)['zones'][0]['id'])->toBe('Sao_Paulo');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === FLEX_SVC.'/coverage/zones/v1?show_availables=true'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('coverageZones: sem show_availables nao manda query', function () {
        Http::fake([FLEX_SVC.'/coverage/zones/v1' => Http::response(['zones' => []])]);

        flexTestMethods()->coverageZones('MLB', 64196652, 738216);
        Http::assertSent(fn ($req) => $req->url() === FLEX_SVC.'/coverage/zones/v1');
    });

    it('updateCoverageZones: PUT coverage/zones/v1 com body zones', function () {
        Http::fake([FLEX_SVC.'/coverage/zones/v1' => Http::response(['zones' => []])]);

        $zones = [['id' => 'Sao_Paulo', 'cutoff' => ['week' => 12, 'saturday' => 12, 'sunday' => 12]]];
        flexTestMethods()->updateCoverageZones('MLB', 64196652, 738216, $zones);
        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === FLEX_SVC.'/coverage/zones/v1'
            && $req['zones'] === $zones);
    });

    it('coverageRadius / updateCoverageRadius: coverage/radius/v1', function () {
        Http::fake([FLEX_SVC.'/coverage/radius/v1*' => Http::response(['radius' => 5000])]);

        expect(flexTestMethods()->coverageRadius('MLB', 64196652, 738216, true)['radius'])->toBe(5000);
        flexTestMethods()->updateCoverageRadius('MLB', 64196652, 738216, 6000);

        Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === FLEX_SVC.'/coverage/radius/v1?show_availables=true');
        Http::assertSent(fn ($req) => $req->method() === 'PUT' && $req->url() === FLEX_SVC.'/coverage/radius/v1' && $req['radius'] === 6000);
    });

    it('deliveryRanges / updateDeliveryRanges: delivery-ranges/v1', function () {
        Http::fake([FLEX_SVC.'/delivery-ranges/v1*' => Http::response(['delivery_window' => 'same_day'])]);

        expect(flexTestMethods()->deliveryRanges('MLB', 64196652, 738216)['delivery_window'])->toBe('same_day');
        $ranges = ['week' => [['capacity' => 30, 'from' => 11, 'to' => 20, 'cutoff' => 14]], 'saturday' => [], 'sunday' => []];
        flexTestMethods()->updateDeliveryRanges('MLB', 64196652, 738216, 'same_day', $ranges);

        Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === FLEX_SVC.'/delivery-ranges/v1');
        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === FLEX_SVC.'/delivery-ranges/v1'
            && $req['delivery_window'] === 'same_day'
            && $req['delivery_ranges'] === $ranges);
    });

    it('holidays / updateHolidays: holidays/v1', function () {
        Http::fake([FLEX_SVC.'/holidays/v1' => Http::response(['holidays' => [['date' => '2021-12-25', 'selected' => true]]])]);

        expect(flexTestMethods()->holidays('MLB', 64196652, 738216)['holidays'][0]['date'])->toBe('2021-12-25');
        $holidays = [['date' => '2021-12-25', 'description' => 'Christmas', 'selected' => false]];
        flexTestMethods()->updateHolidays('MLB', 64196652, 738216, $holidays);

        Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === FLEX_SVC.'/holidays/v1' && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
        Http::assertSent(fn ($req) => $req->method() === 'PUT' && $req->url() === FLEX_SVC.'/holidays/v1' && $req['holidays'] === $holidays);
    });
});
