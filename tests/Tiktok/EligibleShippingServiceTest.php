<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tests\Support\RecursiveFieldCoverage;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\EligibleShippingServiceResponseDTO;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttShipSvcFixture(): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/get-eligible-shipping-service.json'),
        true,
    );
}

function ttShipSvcFulfillment()
{
    return MarketPlaces::Tiktok()->fulfillment(new FakeIntegration(
        accessToken: 'access',
        refreshToken: 'rt',
        settings: ['app_key' => 'ak', 'app_secret' => 'as', 'shop_cipher' => 'cipher1'],
        active: true,
        expired: false,
    ));
}

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function () {
    $payload = ttShipSvcFixture();
    $perdidos = RecursiveFieldCoverage::missing(
        $payload,
        EligibleShippingServiceResponseDTO::fromArray($payload)->toArray(),
    );

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
});

it('faz roundtrip lossless do payload inteiro', function () {
    $payload = ttShipSvcFixture();

    expect(EligibleShippingServiceResponseDTO::fromArray($payload)->toArray())->toEqual($payload);
});

// ─────────────────────────────────────────────────────────────────────────
// Comportamento real
// ─────────────────────────────────────────────────────────────────────────

it('order_line_id e singular no nome mas LISTA no valor', function () {
    $dto = EligibleShippingServiceResponseDTO::fromArray(ttShipSvcFixture());

    // Tipar string aqui derrubaria o campo pro default (null) sem erro nenhum.
    expect($dto->orderLineId)->toBe(['32132124331234']);
});

it('preco e taxas continuam STRING — inclusive os que parecem inteiro', function () {
    $svc = EligibleShippingServiceResponseDTO::fromArray(ttShipSvcFixture())->shippingServices[0];

    expect($svc->price)->toBe('5')
        ->and($svc->shippingFee)->toBe('3')
        ->and($svc->shippingAppServiceFee)->toBe('1')
        ->and($svc->signatureFee)->toBe('1')
        ->and($svc->price)->toBeString();
});

it('is_default=false sobrevive ao roundtrip (nao vira campo omitido)', function () {
    $dto = EligibleShippingServiceResponseDTO::fromArray(ttShipSvcFixture());

    expect($dto->shippingServices[0]->isDefault)->toBeFalse()
        ->and($dto->toArray()['shipping_services'][0])->toHaveKey('is_default');
});

it('devolve o peso/dimensao que a API ASSUMIU pra cotar', function () {
    $dto = EligibleShippingServiceResponseDTO::fromArray(ttShipSvcFixture());

    // A cotacao so' vale pro pacote descrito aqui — conferir antes de confiar no preco.
    expect($dto->weight->value)->toBe('1.2')
        ->and($dto->weight->unit)->toBe('GRAM')
        ->and($dto->dimension->height)->toBe('0.04')
        ->and($dto->dimension->unit)->toBe('INCH');
});

it('getEligibleShippingServices e POST /query com os filtros no CORPO', function () {
    Http::fake([
        '*/fulfillment/202309/orders/28823355942588/shipping_services/query*' => Http::response([
            'code' => 0,
            'message' => 'Success',
            'data' => ttShipSvcFixture(),
        ]),
    ]);

    $dto = ttShipSvcFulfillment()->getEligibleShippingServices(
        '28823355942588',
        orderLineItemIds: ['32132124331234'],
        weight: ['value' => '0.4', 'unit' => 'GRAM'],
        dimension: ['length' => '0.3', 'width' => '0.2', 'height' => '0.1', 'unit' => 'INCH'],
        orderLineList: [['order_line_id' => '57632132124331234', 'sub_item_id' => '9']],
    );

    expect($dto->orderId)->toBe('28823355942588')
        ->and($dto->shippingServices)->toHaveCount(1);

    Http::assertSent(fn ($r) => $r->method() === 'POST'
        && str_contains($r->url(), '/fulfillment/202309/orders/28823355942588/shipping_services/query')
        && str_contains($r->body(), '"order_line_item_ids":["32132124331234"]')
        && str_contains($r->body(), '"value":"0.4"')
        && str_contains($r->body(), '"sub_item_id":"9"'));
});

it('omite do corpo peso, dimensao e listas vazias', function () {
    Http::fake(['*shipping_services/query*' => Http::response(['code' => 0, 'data' => ttShipSvcFixture()])]);

    ttShipSvcFulfillment()->getEligibleShippingServices('28823355942588');

    Http::assertSent(fn ($r) => ! str_contains($r->body(), 'weight')
        && ! str_contains($r->body(), 'dimension')
        && ! str_contains($r->body(), 'order_line_item_ids'));
});
