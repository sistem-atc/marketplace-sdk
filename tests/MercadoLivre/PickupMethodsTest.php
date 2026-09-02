<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Pickup\PickupMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function pickupTestIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function pickupTestMethods(): PickupMethods
{
    $integration = pickupTestIntegration();

    return new PickupMethods(HttpClientFactory::make($integration), $integration);
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

describe('PickupMethods capacidade', function () {
    it('userCapacity: GET /users/{id}/capacity_middleend/cross_docking', function () {
        Http::fake(['api.mercadolibre.com/users/123/capacity_middleend/cross_docking' => Http::response(['capacities' => [['day' => 'monday']]])]);

        expect(pickupTestMethods()->userCapacity(123)['capacities'][0]['day'])->toBe('monday');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/users/123/capacity_middleend/cross_docking'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('updateUserCapacity: PUT com body capacities e logistic_type xd_drop_off', function () {
        Http::fake(['api.mercadolibre.com/users/123/capacity_middleend/xd_drop_off' => Http::response(['ok' => true])]);

        $caps = [['day' => 'monday', 'capacity' => ['value' => 120, 'maximum' => false]]];
        pickupTestMethods()->updateUserCapacity(123, $caps, PickupMethods::LOGISTIC_XD_DROP_OFF);
        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://api.mercadolibre.com/users/123/capacity_middleend/xd_drop_off'
            && $req['capacities'] === $caps);
    });

    it('nodeCapacity / updateNodeCapacity: /nodes/{node}/capacity_middleend', function () {
        Http::fake(['api.mercadolibre.com/nodes/MXP20157465171/capacity_middleend' => Http::response(['capacities' => []])]);

        pickupTestMethods()->nodeCapacity('MXP20157465171');
        $caps = [['day' => 'tuesday', 'capacity' => ['value' => 10, 'maximum' => false]]];
        pickupTestMethods()->updateNodeCapacity('MXP20157465171', $caps);

        Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === 'https://api.mercadolibre.com/nodes/MXP20157465171/capacity_middleend');
        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://api.mercadolibre.com/nodes/MXP20157465171/capacity_middleend'
            && $req['capacities'] === $caps
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });
});

describe('PickupMethods tempo de preparacao', function () {
    it('userProcessingTime: GET com X-Version v3', function () {
        Http::fake(['api.mercadolibre.com/users/123/service/carrier_pickup/processing_time_tool' => Http::response(['monday' => ['enabled' => true]])]);

        expect(pickupTestMethods()->userProcessingTime(123)['monday']['enabled'])->toBeTrue();
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/users/123/service/carrier_pickup/processing_time_tool'
            && $req->hasHeader('X-Version', 'v3')
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('updateUserProcessingTime: PUT com X-Version v3 e body processing_times', function () {
        Http::fake(['api.mercadolibre.com/users/123/service/carrier_pickup/processing_time_tool' => Http::response(['message' => 'ok'])]);

        $times = ['monday' => ['processing_time' => '01:00']];
        pickupTestMethods()->updateUserProcessingTime(123, $times);
        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->hasHeader('X-Version', 'v3')
            && $req['processing_times'] === $times);
    });

    it('nodeProcessingTime / updateNodeProcessingTime: /nodes/{node}/service/{svc}/processing_time_tool', function () {
        Http::fake(['api.mercadolibre.com/nodes/MXP1/service/carrier_pickup/processing_time_tool' => Http::response(['message' => 'ok'])]);

        pickupTestMethods()->nodeProcessingTime('MXP1');
        pickupTestMethods()->updateNodeProcessingTime('MXP1', ['friday' => ['processing_time' => '00:30']]);

        Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === 'https://api.mercadolibre.com/nodes/MXP1/service/carrier_pickup/processing_time_tool');
        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://api.mercadolibre.com/nodes/MXP1/service/carrier_pickup/processing_time_tool'
            && $req->hasHeader('X-Version', 'v3')
            && $req['processing_times']['friday']['processing_time'] === '00:30');
    });
});

describe('PickupMethods agenda e pontos facultativos', function () {
    it('userSchedule: GET /users/{id}/shipping/schedule/cross_docking', function () {
        Http::fake(['api.mercadolibre.com/users/123/shipping/schedule/cross_docking' => Http::response(['schedule' => []])]);

        pickupTestMethods()->userSchedule(123);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/users/123/shipping/schedule/cross_docking'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('nodeSchedule: GET /nodes/{node}/schedule/xd_drop_off', function () {
        Http::fake(['api.mercadolibre.com/nodes/MXP1/schedule/xd_drop_off' => Http::response(['node_id' => 'MXP1'])]);

        expect(pickupTestMethods()->nodeSchedule('MXP1')['node_id'])->toBe('MXP1');
        Http::assertSent(fn ($req) => $req->url() === 'https://api.mercadolibre.com/nodes/MXP1/schedule/xd_drop_off');
    });

    it('workingDays: GET /shipping/seller/{id}/working_day_middleend', function () {
        Http::fake(['api.mercadolibre.com/shipping/seller/123/working_day_middleend' => Http::response(['dates' => [['date' => '2022-09-26']]])]);

        expect(pickupTestMethods()->workingDays(123)['dates'][0]['date'])->toBe('2022-09-26');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/shipping/seller/123/working_day_middleend'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('updateWorkingDays: PUT com site_id + dates', function () {
        Http::fake(['api.mercadolibre.com/shipping/seller/123/working_day_middleend' => Http::response(['ok' => true])]);

        $dates = [['checked' => true, 'description' => 'Feriado', 'date' => '2022-09-26']];
        pickupTestMethods()->updateWorkingDays(123, 'MLB', $dates);
        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://api.mercadolibre.com/shipping/seller/123/working_day_middleend'
            && $req['site_id'] === 'MLB'
            && $req['dates'] === $dates);
    });

    it('workingDayOptout: GET .../optout com e sem date', function () {
        Http::fake(['api.mercadolibre.com/shipping/seller/123/working_day_middleend/optout*' => Http::response(['dates' => []])]);

        pickupTestMethods()->workingDayOptout(123, '2022-10-17');
        pickupTestMethods()->workingDayOptout(123);

        Http::assertSent(fn ($req) => $req->url() === 'https://api.mercadolibre.com/shipping/seller/123/working_day_middleend/optout?date=2022-10-17');
        Http::assertSent(fn ($req) => $req->url() === 'https://api.mercadolibre.com/shipping/seller/123/working_day_middleend/optout');
    });
});
