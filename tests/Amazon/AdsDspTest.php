<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Dsp\DspMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function adsDspIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ads-access-token',
        refreshToken: 'ads-refresh',
        settings: [],
        active: true,
        expired: false,
    );
}

function adsDsp(): DspMethods
{
    return new DspMethods(adsDspIntegration(), 'client-abc');
}

/**
 * Confere verbo + URL completa + headers de auth/scope + media types + body.
 *
 * @param  array<string, string|null>  $headers  Content-Type / Accept / Amazon-Advertising-API-Scope esperados (null = ausente)
 */
function adsDspAssertSent(string $method, string $url, array $headers = [], mixed $body = null): void
{
    Http::assertSent(function (Request $req) use ($method, $url, $headers, $body) {
        if ($req->method() !== $method || $req->url() !== $url) {
            return false;
        }

        if ($req->header('Amazon-Advertising-API-ClientId')[0] !== 'client-abc'
            || $req->header('Authorization')[0] !== 'Bearer ads-access-token') {
            return false;
        }

        // BaseMethods::http() sempre manda acceptJson(); o media type próprio vem
        // como segundo valor do Accept — conferimos presença, não posição.
        foreach ($headers as $name => $expected) {
            $values = $req->header($name);
            $ok = $expected === null ? $values === [] : in_array($expected, $values, true);
            if (! $ok) {
                return false;
            }
        }

        if ($body !== null && json_decode($req->body(), true) !== $body) {
            return false;
        }

        return true;
    });
}

beforeEach(function () {
    Http::preventStrayRequests();
});

const ADS_DSP_HOST = 'https://advertising-api.amazon.com';

describe('DspMethods — orders', function () {
    it('getOrders com filtros comma-delimited e Accept dsporders v2.6', function () {
        Http::fake(['advertising-api.amazon.com/dsp/orders/*' => Http::response(['response' => [['orderId' => 'o1']], 'totalResults' => 1])]);

        $out = adsDsp()->getOrders('111', startIndex: 0, count: 50, statusFilter: ['DELIVERING', 'ENDED'], orderIdFilter: ['o1', 'o2'], advertiserIdFilter: ['adv1']);

        expect($out['totalResults'])->toBe(1);
        adsDspAssertSent('GET', ADS_DSP_HOST.'/dsp/orders/?startIndex=0&count=50&statusFilter=DELIVERING%2CENDED&orderIdFilter=o1%2Co2&advertiserIdFilter=adv1', [
            'Amazon-Advertising-API-Scope' => '111',
            'Accept' => DspMethods::ORDERS_V2_6,
            'Content-Type' => null,
        ]);
    });

    it('getOrder por id com rawurlencode', function () {
        Http::fake(['advertising-api.amazon.com/dsp/orders/*' => Http::response(['orderId' => 'o 1'])]);

        adsDsp()->getOrder('111', 'o 1');

        adsDspAssertSent('GET', ADS_DSP_HOST.'/dsp/orders/o%201', ['Amazon-Advertising-API-Scope' => '111', 'Accept' => DspMethods::ORDERS_V2_6]);
    });

    it('updateOrders manda a lista no body com Content-Type e Accept dsporders v2.6', function () {
        Http::fake(['advertising-api.amazon.com/dsp/orders/' => Http::response([['orderId' => 'o1', 'code' => 'SUCCESS']], 207)]);

        $orders = [['orderId' => 'o1', 'advertiserId' => 'adv1', 'name' => 'Campanha', 'deliveryActivationStatus' => 'INACTIVE']];
        adsDsp()->updateOrders('111', $orders);

        adsDspAssertSent('PUT', ADS_DSP_HOST.'/dsp/orders/', [
            'Amazon-Advertising-API-Scope' => '111',
            'Content-Type' => DspMethods::ORDERS_V2_6,
            'Accept' => DspMethods::ORDERS_V2_6,
        ], $orders);
    });

    it('getConversionTrackings com Accept dsporders v2.1', function () {
        Http::fake(['advertising-api.amazon.com/dsp/orders/o1/conversionTracking' => Http::response(['orderId' => 'o1'])]);

        adsDsp()->getConversionTrackings('111', 'o1');

        adsDspAssertSent('GET', ADS_DSP_HOST.'/dsp/orders/o1/conversionTracking', ['Amazon-Advertising-API-Scope' => '111', 'Accept' => DspMethods::CONVERSION_TRACKING_V2_1]);
    });

    it('updateConversionTrackingProducts aceita 204 sem corpo e usa media type dspproducttracking v1', function () {
        Http::fake(['advertising-api.amazon.com/dsp/orders/o1/conversionTracking/products' => Http::response('', 204)]);

        $body = ['productList' => [['asin' => 'B0001', 'action' => 'ADD']]];
        $out = adsDsp()->updateConversionTrackingProducts('111', 'o1', $body);

        expect($out)->toBe([]);
        adsDspAssertSent('PUT', ADS_DSP_HOST.'/dsp/orders/o1/conversionTracking/products', [
            'Amazon-Advertising-API-Scope' => '111',
            'Content-Type' => DspMethods::PRODUCT_TRACKING_V1,
            'Accept' => 'application/vnd.dsperrors.v1+json',
        ], $body);
    });
});

describe('DspMethods — line items', function () {
    it('getLineItems com filtros e Accept dspbasiclineitems v3', function () {
        Http::fake(['advertising-api.amazon.com/dsp/lineItems/*' => Http::response(['response' => []])]);

        adsDsp()->getLineItems('111', startIndex: 10, count: 5, statusFilter: ['DELIVERING'], orderIdFilter: ['o1'], lineItemIdFilter: ['li1', 'li2']);

        adsDspAssertSent('GET', ADS_DSP_HOST.'/dsp/lineItems/?startIndex=10&count=5&statusFilter=DELIVERING&orderIdFilter=o1&lineItemIdFilter=li1%2Cli2', [
            'Amazon-Advertising-API-Scope' => '111',
            'Accept' => DspMethods::BASIC_LINE_ITEMS_V3,
        ]);
    });

    it('updateLineItems com Content-Type dsplineitems v3.3 e Accept dsplineitemsresponse v3.1', function () {
        Http::fake(['advertising-api.amazon.com/dsp/lineItems/' => Http::response([['lineItemId' => 'li1', 'code' => 'SUCCESS']], 207)]);

        $items = [['lineItemId' => 'li1', 'orderId' => 'o1', 'lineItemType' => 'VIDEO', 'name' => 'LI', 'deliveryActivationStatus' => 'ACTIVE']];
        adsDsp()->updateLineItems('111', $items);

        adsDspAssertSent('PUT', ADS_DSP_HOST.'/dsp/lineItems/', [
            'Amazon-Advertising-API-Scope' => '111',
            'Content-Type' => DspMethods::LINE_ITEMS_V3_3,
            'Accept' => DspMethods::LINE_ITEMS_RESPONSE_V3_1,
        ], $items);
    });
});

describe('DspMethods — creatives', function () {
    it('getCreatives com filtros e Accept dspcreatives v2.1', function () {
        Http::fake(['advertising-api.amazon.com/dsp/creatives/*' => Http::response(['response' => []])]);

        adsDsp()->getCreatives('111', startIndex: 0, count: 20, creativeIdFilter: ['c1'], advertiserIdFilter: ['adv1'], lineItemTypeFilter: 'VIDEO');

        adsDspAssertSent('GET', ADS_DSP_HOST.'/dsp/creatives/?startIndex=0&count=20&creativeIdFilter=c1&advertiserIdFilter=adv1&lineItemTypeFilter=VIDEO', [
            'Amazon-Advertising-API-Scope' => '111',
            'Accept' => DspMethods::CREATIVES_V2_1,
        ]);
    });

    $legacyGets = [
        ['getImageCreatives', '/dsp/creatives/image', 'application/vnd.dspimagecreatives.v1+json'],
        ['getVideoCreatives', '/dsp/creatives/video', 'application/vnd.dspvideocreatives.v1+json'],
        ['getRecCreatives', '/dsp/creatives/rec', 'application/vnd.dspreccreatives.v1+json'],
        ['getThirdPartyCreatives', '/dsp/creatives/thirdparty', 'application/vnd.dspthirdpartycreatives.v1+json'],
        ['getCreativeModeration', '/dsp/moderation/creatives', 'application/vnd.dspmoderationcreatives.v1+json'],
    ];

    foreach ($legacyGets as [$method, $path, $accept]) {
        it("{$method} (deprecated) exige creativeIdFilter e Accept {$accept}", function () use ($method, $path, $accept) {
            Http::fake(['advertising-api.amazon.com/*' => Http::response(['response' => []])]);

            adsDsp()->{$method}('111', ['c1', 'c2']);

            adsDspAssertSent('GET', ADS_DSP_HOST.$path.'?creativeIdFilter=c1%2Cc2', [
                'Amazon-Advertising-API-Scope' => '111',
                'Accept' => $accept,
            ]);
        });
    }

    $legacyWrites = [
        ['createImageCreative', 'POST', '/dsp/creatives/image', 'application/vnd.dspcreateimagecreatives.v1+json', 'application/vnd.dspimagecreativesresponse.v1+json'],
        ['updateImageCreative', 'PUT', '/dsp/creatives/image', 'application/vnd.dspupdateimagecreatives.v1+json', 'application/vnd.dspimagecreativesresponse.v1+json'],
        ['previewImageCreative', 'POST', '/dsp/creatives/image/preview', 'application/vnd.dsppreviewimagecreatives.v1+json', 'application/vnd.dsppreviewcreativesresponse.v1+json'],
        ['createVideoCreatives', 'POST', '/dsp/creatives/video', 'application/vnd.dspcreatevideocreatives.v1+json', 'application/vnd.dspvideocreativesresponse.v1+json'],
        ['updateVideoCreatives', 'PUT', '/dsp/creatives/video', 'application/vnd.dspupdatevideocreatives.v1+json', 'application/vnd.dspvideocreativesresponse.v1+json'],
        ['previewVideoCreative', 'POST', '/dsp/creatives/video/preview', 'application/vnd.dsppreviewvideocreatives.v1+json', 'application/vnd.dsppreviewcreativesresponse.v1+json'],
        ['createRecCreatives', 'POST', '/dsp/creatives/rec', 'application/vnd.dspcreatereccreatives.v1+json', 'application/vnd.dspreccreativesresponse.v1+json'],
        ['updateRecCreatives', 'PUT', '/dsp/creatives/rec', 'application/vnd.dspupdatereccreatives.v1+json', 'application/vnd.dspreccreativesresponse.v1+json'],
        ['previewRecCreative', 'POST', '/dsp/creatives/rec/preview', 'application/vnd.dsppreviewreccreatives.v1+json', 'application/vnd.dsppreviewcreativesresponse.v1+json'],
        ['createThirdPartyCreative', 'POST', '/dsp/creatives/thirdparty', 'application/vnd.dspcreatethirdpartycreatives.v1+json', 'application/vnd.dspthirdpartycreativesresponse.v1+json'],
        ['updateThirdPartyCreative', 'PUT', '/dsp/creatives/thirdparty', 'application/vnd.dspupdatethirdpartycreatives.v1+json', 'application/vnd.dspthirdpartycreativesresponse.v1+json'],
        ['previewThirdPartyCreative', 'POST', '/dsp/creatives/thirdparty/preview', 'application/vnd.dsppreviewthirdpartycreatives.v1+json', 'application/vnd.dsppreviewcreativesresponse.v1+json'],
    ];

    foreach ($legacyWrites as [$method, $verb, $path, $contentType, $accept]) {
        it("{$method} (deprecated) {$verb} {$path} com media types próprios e body", function () use ($method, $verb, $path, $contentType, $accept) {
            Http::fake(['advertising-api.amazon.com/*' => Http::response(['response' => []])]);

            $body = str_starts_with($method, 'preview')
                ? ['creativeId' => 'c1', 'previewConfiguration' => ['size' => ['width' => 300, 'height' => 250]]]
                : [['name' => 'Cr', 'advertiserId' => 'adv1', 'creativeId' => 'c1']];

            adsDsp()->{$method}('111', $body);

            adsDspAssertSent($verb, ADS_DSP_HOST.$path, [
                'Amazon-Advertising-API-Scope' => '111',
                'Content-Type' => $contentType,
                'Accept' => $accept,
            ], $body);
        });
    }

    it('associateLineItemsToCreatives monta advertiserId/operation/associations (v2.1)', function () {
        Http::fake(['advertising-api.amazon.com/dsp/lineItemCreativeAssociations' => Http::response(['response' => []])]);

        adsDsp()->associateLineItemsToCreatives('111', 'adv1', 'CREATE', [['lineItemId' => 'li1', 'creativeId' => 'c1']]);

        adsDspAssertSent('POST', ADS_DSP_HOST.'/dsp/lineItemCreativeAssociations', [
            'Amazon-Advertising-API-Scope' => '111',
            'Content-Type' => 'application/vnd.dsplineitemcreativeassociations.v2.1+json',
            'Accept' => 'application/vnd.dsplineitemcreativeassociations.v2.1+json',
        ], ['advertiserId' => 'adv1', 'operation' => 'CREATE', 'associations' => [['lineItemId' => 'li1', 'creativeId' => 'c1']]]);
    });
});

describe('DspMethods — discovery e advertisers', function () {
    it('getGeoLocations pagina por nextToken/maxResults', function () {
        Http::fake(['advertising-api.amazon.com/dsp/geoLocations*' => Http::response(['geoLocations' => [], 'nextToken' => 'n2'])]);

        $out = adsDsp()->getGeoLocations('111', geoLocationIdFilter: ['g1', 'g2'], textQuery: 'São Paulo', nextToken: 'n1', maxResults: 25);

        expect($out['nextToken'])->toBe('n2');
        adsDspAssertSent('GET', ADS_DSP_HOST.'/dsp/geoLocations?geoLocationIdFilter=g1%2Cg2&textQuery=S%C3%A3o%20Paulo&nextToken=n1&maxResults=25', [
            'Amazon-Advertising-API-Scope' => '111',
            'Accept' => 'application/json',
        ]);
    });

    it('getAdvertisers (v3.0) usa startIndex/count/advertiserIdFilter e Scope = profile DSP', function () {
        Http::fake(['advertising-api.amazon.com/dsp/advertisers*' => Http::response(['response' => [['advertiserId' => 'adv1']], 'totalResults' => 1])]);

        adsDsp()->getAdvertisers('111', startIndex: 0, count: 100, advertiserIdFilter: ['adv1', 'adv2']);

        adsDspAssertSent('GET', ADS_DSP_HOST.'/dsp/advertisers?startIndex=0&count=100&advertiserIdFilter=adv1%2Cadv2', [
            'Amazon-Advertising-API-Scope' => '111',
            'Accept' => 'application/json',
        ]);
    });

    it('getAdvertiser (v3.0) coloca o advertiserId no path, Scope continua o profile', function () {
        Http::fake(['advertising-api.amazon.com/dsp/advertisers/*' => Http::response(['advertiserId' => 'adv1', 'name' => 'Soldiers'])]);

        $out = adsDsp()->getAdvertiser('111', 'adv1');

        expect($out['name'])->toBe('Soldiers');
        adsDspAssertSent('GET', ADS_DSP_HOST.'/dsp/advertisers/adv1', ['Amazon-Advertising-API-Scope' => '111', 'Accept' => 'application/json']);
    });
});

describe('DspMethods — reports v3', function () {
    it('createReport usa accountId no path, media type dspcreatereports v3 e sem Scope por padrão', function () {
        Http::fake(['advertising-api.amazon.com/accounts/ENT1/dsp/reports' => Http::response(['reportId' => 'r1', 'status' => 'IN_PROGRESS'])]);

        $body = ['startDate' => '2026-08-01', 'endDate' => '2026-08-31', 'type' => 'CAMPAIGN', 'format' => 'JSON', 'timeUnit' => 'DAILY', 'metrics' => ['impressions', 'totalCost']];
        $out = adsDsp()->createReport('ENT1', $body);

        expect($out['reportId'])->toBe('r1');
        adsDspAssertSent('POST', ADS_DSP_HOST.'/accounts/ENT1/dsp/reports', [
            'Content-Type' => DspMethods::REPORTS_CREATE_V3,
            'Accept' => DspMethods::REPORTS_CREATE_V3,
            'Amazon-Advertising-API-Scope' => null,
        ], $body);
    });

    it('createReport manda o Scope quando o profileId é informado', function () {
        Http::fake(['advertising-api.amazon.com/accounts/ENT1/dsp/reports' => Http::response(['reportId' => 'r1'])]);

        adsDsp()->createReport('ENT1', ['startDate' => '2026-08-01', 'endDate' => '2026-08-02'], profileId: '111');

        adsDspAssertSent('POST', ADS_DSP_HOST.'/accounts/ENT1/dsp/reports', ['Amazon-Advertising-API-Scope' => '111']);
    });

    it('getReport devolve status/location com Accept dspgetreports v3', function () {
        Http::fake(['advertising-api.amazon.com/accounts/ENT1/dsp/reports/r1' => Http::response(['reportId' => 'r1', 'status' => 'SUCCESS', 'location' => 'https://s3.fake/r1.json'])]);

        $out = adsDsp()->getReport('ENT1', 'r1');

        expect($out['location'])->toBe('https://s3.fake/r1.json');
        adsDspAssertSent('GET', ADS_DSP_HOST.'/accounts/ENT1/dsp/reports/r1', [
            'Accept' => DspMethods::REPORTS_GET_V3,
            'Amazon-Advertising-API-Scope' => null,
        ]);
    });

    it('erro HTTP vira AmazonAdsRequestException', function () {
        Http::fake(['advertising-api.amazon.com/dsp/orders/o9' => Http::response(['message' => 'Order not found'], 404)]);

        adsDsp()->getOrder('111', 'o9');
    })->throws(AmazonAdsRequestException::class, 'Order not found');
});
