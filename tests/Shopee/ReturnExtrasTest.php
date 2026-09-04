<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Return\ReturnMethods;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeRequestException;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function returnExtrasClient(): ReturnMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );

    return new ReturnMethods(HttpClientFactory::make($integration), $integration);
}

/** Conteudo de uma parte multipart pelo name. */
function returnExtrasPart(\Illuminate\Http\Client\Request $req, string $name): ?string
{
    foreach ($req->data() as $part) {
        if (($part['name'] ?? null) === $name) return $part['contents'];
    }

    return null;
}

function returnExtrasQuery(string $url): array
{
    parse_str((string) parse_url($url, PHP_URL_QUERY), $q);

    return $q;
}

function returnExtrasAssertShop(string $method, string $path): void
{
    Http::assertSent(function ($req) use ($method, $path) {
        $q = returnExtrasQuery($req->url());

        return $req->method() === $method
            && str_contains($req->url(), $path)
            && ($q['partner_id'] ?? null) === '2030136'
            && ($q['shop_id'] ?? null) === '999999'
            && isset($q['sign'], $q['timestamp'], $q['access_token']);
    });
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

$ok = fn (array $response = []) => Http::response(['error' => '', 'message' => '', 'request_id' => 'x', 'response' => $response]);

describe('ReturnMethods extras — GET por return_sn', function () use ($ok) {
    it('get_available_solutions, get_return_dispute_reason, query_proof, get_shipping_carrier, get_reverse_tracking_info', function () use ($ok) {
        Http::fake(['*' => $ok(['ok' => true])]);
        $c = returnExtrasClient();
        $c->getAvailableSolutions('R1');
        $c->getReturnDisputeReason('R1');
        $c->queryProof('R1');
        $c->getShippingCarrier('R1');
        $c->getReverseTrackingInfo('R1');
        foreach (['get_available_solutions', 'get_return_dispute_reason', 'query_proof', 'get_shipping_carrier', 'get_reverse_tracking_info'] as $p) {
            returnExtrasAssertShop('GET', '/api/v2/returns/'.$p);
        }
        Http::assertSentCount(5);
        Http::assertSent(fn ($req) => returnExtrasQuery($req->url())['return_sn'] === 'R1');
    });
});

describe('ReturnMethods extras — negociacao e disputa', function () use ($ok) {
    it('offer manda proposed_solution e valor opcional', function () use ($ok) {
        Http::fake(['*/api/v2/returns/offer*' => $ok()]);
        returnExtrasClient()->offer('R1', 'REFUND', 12.5);
        returnExtrasAssertShop('POST', '/api/v2/returns/offer');
        Http::assertSent(fn ($req) => $req->data() === ['return_sn' => 'R1', 'proposed_solution' => 'REFUND', 'proposed_adjusted_refund_amount' => 12.5]);
    });

    it('acceptOffer manda return_sn', function () use ($ok) {
        Http::fake(['*/api/v2/returns/accept_offer*' => $ok()]);
        returnExtrasClient()->acceptOffer('R1');
        returnExtrasAssertShop('POST', '/api/v2/returns/accept_offer');
        Http::assertSent(fn ($req) => $req->data() === ['return_sn' => 'R1']);
    });

    it('dispute manda email, reason id, image_list e texto', function () use ($ok) {
        Http::fake(['*/api/v2/returns/dispute*' => $ok()]);
        returnExtrasClient()->dispute('R1', 'ops@x.com', 3, [['module_index' => 0, 'image_url' => ['http://i/1']]], 'produto ok');
        returnExtrasAssertShop('POST', '/api/v2/returns/dispute');
        Http::assertSent(fn ($req) => $req->data() === [
            'return_sn' => 'R1', 'email' => 'ops@x.com', 'dispute_reason_id' => 3,
            'image_list' => [['module_index' => 0, 'image_url' => ['http://i/1']]],
            'dispute_text_reason' => 'produto ok',
        ]);
    });

    it('cancelDispute manda return_sn + email', function () use ($ok) {
        Http::fake(['*/api/v2/returns/cancel_dispute*' => $ok()]);
        returnExtrasClient()->cancelDispute('R1', 'ops@x.com');
        returnExtrasAssertShop('POST', '/api/v2/returns/cancel_dispute');
        Http::assertSent(fn ($req) => $req->data() === ['return_sn' => 'R1', 'email' => 'ops@x.com']);
    });
});

describe('ReturnMethods extras — provas', function () use ($ok) {
    it('convertImage sobe multipart upload_image + return_sn', function () use ($ok) {
        Http::fake(['*/api/v2/returns/convert_image*' => $ok(['url' => 'http://u', 'thumbnail' => 'http://t'])]);
        $r = returnExtrasClient()->convertImage('R1', 'PNGDATA', 'p.png');
        expect($r['response']['url'])->toBe('http://u');
        returnExtrasAssertShop('POST', '/api/v2/returns/convert_image');
        Http::assertSent(fn ($req) => $req->isMultipart() && $req->hasFile('upload_image', 'PNGDATA', 'p.png') && returnExtrasPart($req, 'return_sn') === 'R1');
    });

    it('convertImage com erro de negocio lanca ShopeeRequestException', function () {
        Http::fake(['*/api/v2/returns/convert_image*' => Http::response(['error' => 'error_param', 'message' => 'x'])]);
        expect(fn () => returnExtrasClient()->convertImage('R1', 'x', 'p.png'))->toThrow(ShopeeRequestException::class);
    });

    it('uploadProof manda photo[] + description', function () use ($ok) {
        Http::fake(['*/api/v2/returns/upload_proof*' => $ok()]);
        returnExtrasClient()->uploadProof('R1', [['url' => 'http://u', 'thumbnail' => 'http://t']], 'foto');
        returnExtrasAssertShop('POST', '/api/v2/returns/upload_proof');
        Http::assertSent(fn ($req) => $req->data() === ['return_sn' => 'R1', 'photo' => [['url' => 'http://u', 'thumbnail' => 'http://t']], 'description' => 'foto']);
    });

    it('uploadShippingProof manda carrier + tracking + image_id_list', function () use ($ok) {
        Http::fake(['*/api/v2/returns/upload_shipping_proof*' => $ok()]);
        returnExtrasClient()->uploadShippingProof('R1', 55, 'Correios', 'BR1', [['image_id' => 'i1']], 'ok');
        returnExtrasAssertShop('POST', '/api/v2/returns/upload_shipping_proof');
        Http::assertSent(fn ($req) => $req->data() === [
            'return_sn' => 'R1', 'reverse_logistics_carrier_id' => 55, 'reverse_logistics_carrier_name' => 'Correios',
            'tracking_number' => 'BR1', 'image_id_list' => [['image_id' => 'i1']], 'remarks' => 'ok',
        ]);
    });
});
