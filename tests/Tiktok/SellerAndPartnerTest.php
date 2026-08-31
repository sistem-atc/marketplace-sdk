<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Authorization\CategoryAsset;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\UploadedImage;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Seller\ShopGroupData;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttSellerFixture(string $slug): array
{
    return json_decode(file_get_contents(__DIR__."/../Fixtures/Tiktok/{$slug}.json"), true);
}

function ttSellerIntegration(): FakeIntegration
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
function ttSellerPerdidos(array $payload, array $serializado): array
{
    $perdidos = [];
    foreach ($payload as $chave => $valor) {
        if (! array_key_exists($chave, $serializado)) {
            $perdidos[] = (string) $chave;

            continue;
        }
        if (is_array($valor) && is_array($serializado[$chave])) {
            foreach (ttSellerPerdidos($valor, $serializado[$chave]) as $sub) {
                $perdidos[] = $chave.'.'.$sub;
            }
        }
    }

    return $perdidos;
}

it('nao descarta NENHUM campo dos exemplos OFICIAIS da doc', function (string $slug, string $classe) {
    $payload = ttSellerFixture($slug);

    $perdidos = ttSellerPerdidos($payload, $classe::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], "{$slug}: campos descartados: ".implode(', ', $perdidos));
})->with([
    ['get-shop-groups', ShopGroupData::class],
    ['get-authorized-category-assets', CategoryAsset::class],
    ['upload-buyer-messages-image', UploadedImage::class],
]);

it('getPermissions devolve lista crua e lista VAZIA e resposta valida', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/seller/202309/permissions*' => Http::sequence()
            ->push(['code' => 0, 'data' => ttSellerFixture('get-seller-permissions')])
            // Seller sem permissao cross-border: `permissions` some da resposta.
            ->push(['code' => 0, 'data' => []]),
    ]);

    $api = MarketPlaces::Tiktok()->seller(ttSellerIntegration());

    expect($api->getPermissions())->toBe(['MANAGE_GLOBAL_PRODUCT'])
        ->and($api->getPermissions())->toBe([]);
});

it('getShopGroups devolve UM grupo (nao lista) com as lojas dentro', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/seller/202601/shop_groups*' => Http::response([
            'code' => 0, 'data' => ['shop_group_data' => ttSellerFixture('get-shop-groups')],
        ]),
    ]);

    $g = MarketPlaces::Tiktok()->seller(ttSellerIntegration())->getShopGroups();

    // `source` e' o CODIGO em string ("1" = US_MX_ONBOARD), nao o nome do enum.
    expect($g->shopGroup->source)->toBe('1')
        ->and($g->shopGroup->shopGroupName)->toBe('shop group name A')
        ->and($g->shops)->toHaveCount(1)
        ->and($g->shops[0]->shopId)->toBe('36123502970007')
        // A loja do grupo NAO traz shop_cipher — ele so vem do getAuthorizedShops.
        ->and($g->shops[0]->toArray())->not->toHaveKey('cipher');
});

it('category asset traz cipher de PARCEIRO (TTP_), nao shop_cipher', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/authorization/202405/category_assets*' => Http::response([
            'code' => 0, 'data' => ['category_assets' => [ttSellerFixture('get-authorized-category-assets')]],
        ]),
    ]);

    $assets = MarketPlaces::Tiktok()->authorization(ttSellerIntegration())->getAuthorizedCategoryAssets();

    expect($assets[0]->cipher)->toStartWith('TTP_')
        ->and($assets[0]->targetMarket)->toBe('US')
        // Excecao a regra "id e string": aqui e' codigo INT de categoria de
        // negocio, nao snowflake. Compare pelo id — o `name` muda.
        ->and($assets[0]->category->id)->toBe(3)
        ->and($assets[0]->category->name)->toBe('Customer Support');
});

it('uploadBuyerMessagesImage sobe multipart e devolve a URL hospedada', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/customer_service/202309/images/upload*' => Http::response([
            'code' => 0, 'data' => ttSellerFixture('upload-buyer-messages-image'),
        ]),
    ]);

    $arquivo = tempnam(sys_get_temp_dir(), 'tt').'.png';
    file_put_contents($arquivo, 'fake-png-bytes');

    $img = MarketPlaces::Tiktok()->customerService(ttSellerIntegration())
        ->uploadBuyerMessagesImage($arquivo);

    // O upload NAO envia nada pro comprador: e' a `url` que vai em sendMessage.
    expect($img->url)->toStartWith('https://p16-oec-va.ibyteimg.com/')
        ->and($img->width)->toBe(1280)
        ->and($img->height)->toBe(720);

    Http::assertSent(fn ($req) => $req->method() === 'POST'
        && str_contains($req->url(), '/customer_service/202309/images/upload')
        && str_contains($req->url(), 'sign='));

    unlink($arquivo);
});
