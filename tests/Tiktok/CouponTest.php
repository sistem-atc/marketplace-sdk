<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion\Coupon;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion\CouponSearchResponseDTO;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttCouponFixture(string $slug): array
{
    return json_decode(file_get_contents(__DIR__."/../Fixtures/Tiktok/{$slug}.json"), true);
}

function ttCouponIntegration(): FakeIntegration
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
function ttCouponPerdidos(array $payload, array $serializado): array
{
    $perdidos = [];
    foreach ($payload as $chave => $valor) {
        if (! array_key_exists($chave, $serializado)) {
            $perdidos[] = (string) $chave;

            continue;
        }
        if (is_array($valor) && is_array($serializado[$chave])) {
            foreach (ttCouponPerdidos($valor, $serializado[$chave]) as $sub) {
                $perdidos[] = $chave.'.'.$sub;
            }
        }
    }

    return $perdidos;
}

it('nao descarta NENHUM campo do exemplo OFICIAL do Get Coupon', function () {
    $payload = ttCouponFixture('get-coupon');

    $perdidos = ttCouponPerdidos($payload, Coupon::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
});

it('nao descarta NENHUM campo do exemplo OFICIAL do Search Coupon List', function () {
    $payload = ttCouponFixture('search-coupon-list');

    $perdidos = ttCouponPerdidos($payload, CouponSearchResponseDTO::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
});

it('mistura DUAS escalas de tempo na mesma resposta', function () {
    $c = Coupon::fromArray(ttCouponFixture('get-coupon'));

    // create/update em MILISSEGUNDOS; as durations em SEGUNDOS. Tratar tudo
    // igual joga metade das datas pra 1970 ou pro ano 54000.
    expect($c->createTime)->toBe(1661756811000)
        ->and(date('Y', (int) ($c->createTime / 1000)))->toBe('2022')
        ->and($c->claimDuration->startTime)->toBe(1709568000)
        ->and(date('Y', $c->claimDuration->startTime))->toBe('2024');
});

it('dinheiro do cupom e STRING e a porcentagem e em PONTOS percentuais', function () {
    $c = Coupon::fromArray(ttCouponFixture('get-coupon'));

    expect($c->discount->reductionAmount->amount)->toBe('30.5')
        ->and($c->threshold->minSpend->amount)->toBe('30')
        // "30" = 30%, nao 0,30 — multiplicar por 100 dobra o desconto.
        ->and($c->discount->percentage)->toBe('30');
});

it('separa RESGATADO de USADO', function () {
    $c = Coupon::fromArray(ttCouponFixture('get-coupon'));

    // 5 pessoas pegaram o cupom, ZERO usou. Só redeemedCount virou desconto.
    expect($c->usageStats->claimedCount)->toBe(5)
        ->and($c->usageStats->redeemedCount)->toBe(0)
        // 0 nao pode ser omitido na serializacao — senao "nunca usado" some.
        ->and($c->usageStats->toArray())->toHaveKey('redeemed_count');
});

it('a listagem devolve um SUBCONJUNTO do cupom — usage_stats so vem no detalhe', function () {
    $lista = CouponSearchResponseDTO::fromArray(ttCouponFixture('search-coupon-list'));

    expect($lista->totalCount)->toBe(20)
        ->and($lista->nextPageToken)->toBe('1661756811000')
        ->and($lista->coupons)->toHaveCount(1)
        ->and($lista->coupons[0]->id)->toBe('7342461823095965461')
        // Ausente na listagem: quem precisa de uso tem que chamar getCoupon().
        ->and($lista->coupons[0]->usageStats)->toBeNull()
        ->and($lista->coupons[0]->productIds)->toBeNull();
});

it('getCoupon usa GET /promotion/202406/coupons/{id} — versao diferente das activities', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/promotion/202406/coupons/C1*' => Http::response([
            'code' => 0, 'data' => ['coupon' => ttCouponFixture('get-coupon')],
        ]),
    ]);

    $c = MarketPlaces::Tiktok()->promotion(ttCouponIntegration())->getCoupon('C1');

    expect($c->title)->toBe('Coupon202407')
        ->and($c->liveTasks[0]->minWatchTime)->toBe('60');

    Http::assertSent(fn ($req) => $req->method() === 'GET'
        && str_contains($req->url(), '/promotion/202406/coupons/C1'));
});

it('searchCoupons manda paginacao na QUERY e filtros (listas) no BODY', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/promotion/202406/coupons/search*' => Http::response([
            'code' => 0, 'data' => ttCouponFixture('search-coupon-list'),
        ]),
    ]);

    $r = MarketPlaces::Tiktok()->promotion(ttCouponIntegration())
        ->searchCoupons(pageSize: 50, status: ['ONGOING'], displayType: ['PROMO_CODE']);

    expect($r->coupons[0]->promoCode)->toBe('TTS12345');

    Http::assertSent(fn ($req) => $req->method() === 'POST'
        && str_contains($req->url(), 'page_size=50')
        // status e display_type sao LISTAS, nao string.
        && str_contains($req->body(), '"status":["ONGOING"]')
        && str_contains($req->body(), '"display_type":["PROMO_CODE"]'));
});
