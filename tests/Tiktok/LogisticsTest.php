<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics\AvailableShippingTemplate;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics\DeliveryOption;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics\GlobalWarehouse;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics\ShippingProvider;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics\Warehouse;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttLogisticsFixture(string $slug): array
{
    return json_decode(file_get_contents(__DIR__."/../Fixtures/Tiktok/{$slug}.json"), true);
}

function ttLogisticsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'access',
        refreshToken: 'rt',
        settings: ['app_key' => 'ak', 'app_secret' => 'as', 'shop_cipher' => 'cipher1'],
        active: true,
        expired: false,
    );
}

/** @return list<string> caminhos de chaves do payload que o DTO não devolveu */
function ttLogisticsPerdidos(array $payload, array $serializado): array
{
    $perdidos = [];
    foreach ($payload as $chave => $valor) {
        if (! array_key_exists($chave, $serializado)) {
            $perdidos[] = (string) $chave;

            continue;
        }
        if (is_array($valor) && is_array($serializado[$chave])) {
            foreach (ttLogisticsPerdidos($valor, $serializado[$chave]) as $sub) {
                $perdidos[] = $chave.'.'.$sub;
            }
        }
    }

    return $perdidos;
}

it('nao descarta NENHUM campo dos exemplos OFICIAIS da doc', function (string $slug, string $classe) {
    $payload = ttLogisticsFixture($slug);

    $perdidos = ttLogisticsPerdidos($payload, $classe::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], "{$slug}: campos descartados: ".implode(', ', $perdidos));
})->with([
    ['get-warehouse-list', Warehouse::class],
    ['get-warehouse-delivery-options', DeliveryOption::class],
    ['get-shipping-providers', ShippingProvider::class],
    ['get-global-seller-warehouse', GlobalWarehouse::class],
    ['get-available-shipping-template', AvailableShippingTemplate::class],
]);

it('preserva o `distict` com o erro de grafia da propria API', function () {
    $w = Warehouse::fromArray(ttLogisticsFixture('get-warehouse-list'));

    // A API escreve "distict" (sem o 'r'). Corrigir pra "district" no DTO
    // faria o campo sumir na hidratacao, em silencio.
    expect($w->address->distict)->toBe('HuaDu')
        ->and($w->address->toArray())->toHaveKey('distict')
        ->and($w->address->toArray())->not->toHaveKey('district');
});

it('address_line no BR nao segue a ordem intuitiva', function () {
    $w = Warehouse::fromArray(ttLogisticsFixture('get-warehouse-list'));

    // line1 = bairro, line2 = logradouro, line3 = numero, line4 = complemento.
    // Concatenar na ordem numerica produz endereco invertido.
    expect($w->address->addressLine1)->toBe('Bairro/Distrito')
        ->and($w->address->addressLine2)->toBe('Caminho Trinta e Três')
        ->and($w->address->addressLine3)->toBe('3')
        ->and($w->address->addressLine4)->toBe('11 floor');
});

it('lat/long ficam STRING — nao viram float', function () {
    $w = Warehouse::fromArray(ttLogisticsFixture('get-warehouse-list'));

    expect($w->address->geolocation->latitude)->toBe('45.41634')
        ->and($w->address->geolocation->longitude)->toBe('-75.6868');
});

it('limites da delivery option carregam a UNIDADE junto', function () {
    $o = DeliveryOption::fromArray(ttLogisticsFixture('get-warehouse-delivery-options'));

    // 100 INCH e 100 GRAM: comparar o numero sem olhar a unidade e' o erro
    // classico — os dois campos vem com escalas diferentes na mesma resposta.
    expect($o->dimensionLimit->unit)->toBe('INCH')
        ->and($o->dimensionLimit->maxHeight)->toBe(100)
        ->and($o->weightLimit->unit)->toBe('GRAM')
        ->and($o->weightLimit->minWeight)->toBe(100)
        ->and($o->platform)->toBe(['TOKOPEDIA', 'TIKTOK_SHOP']);
});

it('template indisponivel diz o porque, e o filter_type e STRING', function () {
    $t = AvailableShippingTemplate::fromArray(ttLogisticsFixture('get-available-shipping-template'));

    // A lista traz templates que NAO servem; templateIsAvailable e' o filtro.
    expect($t->templateIsAvailable)->toBeFalse()
        ->and($t->template->templateId)->toBe('1234566')
        ->and($t->template->templateParty)->toBe(1)
        // "9" (string) apesar de ser codigo numerico — comparar com 9 int falha.
        ->and($t->serviceUnreachableReason[0]->filterReason[0]->filterType)->toBe('9')
        ->and($t->serviceUnreachableReason[0]->filterReason[0]->reason)->toBe('Ship by seller is banned');
});

it('getWarehouseList / getWarehouseDeliveryOptions / getShippingProviders montam a cadeia de paths', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/logistics/202309/warehouses?*' => Http::response([
            'code' => 0, 'data' => ['warehouses' => [ttLogisticsFixture('get-warehouse-list')]],
        ]),
        'open-api.tiktokglobalshop.com/logistics/202309/warehouses/W1/delivery_options*' => Http::response([
            'code' => 0, 'data' => ['delivery_options' => [ttLogisticsFixture('get-warehouse-delivery-options')]],
        ]),
        'open-api.tiktokglobalshop.com/logistics/202309/delivery_options/*/shipping_providers*' => Http::response([
            'code' => 0, 'data' => ['shipping_providers' => [ttLogisticsFixture('get-shipping-providers')]],
        ]),
    ]);

    $api = MarketPlaces::Tiktok()->logistics(ttLogisticsIntegration());

    expect($api->getWarehouseList()[0]->id)->toBe('7000714532876273410');

    $opcoes = $api->getWarehouseDeliveryOptions('W1', scope: 'PRODUCT');
    expect($opcoes[0]->id)->toBe('6955034615128000261');

    $provedores = $api->getShippingProviders($opcoes[0]->id, buyerRegion: 'BR');
    expect($provedores[0]->name)->toBe('USPS');

    Http::assertSent(fn ($req) => str_contains($req->url(), 'scope=PRODUCT'));
    Http::assertSent(fn ($req) => str_contains(
        $req->url(),
        '/logistics/202309/delivery_options/6955034615128000261/shipping_providers',
    ) && str_contains($req->url(), 'buyer_region=BR'));
});

it('getAvailableShippingTemplates faz GET COM CORPO (peso/dimensao no body)', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/logistics/202510/seller_templates*' => Http::response([
            'code' => 0, 'data' => ['templates' => [ttLogisticsFixture('get-available-shipping-template')]],
        ]),
    ]);

    MarketPlaces::Tiktok()->logistics(ttLogisticsIntegration())->getAvailableShippingTemplates([
        'weight' => ['weight' => '500', 'unit' => 1],
    ]);

    // Se o body sumisse no GET, a assinatura (que ja o inclui) nao bateria.
    Http::assertSent(fn ($req) => $req->method() === 'GET'
        && str_contains($req->body(), '"weight":"500"'));
});

it('shippingTemplateExists devolve bool e manda biz_warehouse_id na query', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/logistics/202606/seller_template/template_exist*' => Http::response([
            'code' => 0, 'data' => ttLogisticsFixture('shipping-template-exist'),
        ]),
    ]);

    $existe = MarketPlaces::Tiktok()->logistics(ttLogisticsIntegration())
        ->shippingTemplateExists(bizWarehouseId: 123456);

    expect($existe)->toBeFalse();

    Http::assertSent(fn ($req) => str_contains($req->url(), 'biz_warehouse_id=123456'));
});

it('getGlobalSellerWarehouses distingue galpao do seller do galpao do TikTok', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/logistics/202309/global_warehouses*' => Http::response([
            'code' => 0, 'data' => ['global_warehouses' => [ttLogisticsFixture('get-global-seller-warehouse')]],
        ]),
    ]);

    $galpoes = MarketPlaces::Tiktok()->logistics(ttLogisticsIntegration())->getGlobalSellerWarehouses();

    expect($galpoes[0]->ownership)->toBe('SELLER')
        ->and($galpoes[0]->id)->toBe('7000714532876273411');
});
