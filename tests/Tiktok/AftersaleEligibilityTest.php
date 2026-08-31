<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tests\Support\RecursiveFieldCoverage;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\AftersaleEligibilityResponseDTO;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttEligFixture(): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/get-aftersale-eligibility.json'),
        true,
    );
}

function ttEligReverse()
{
    return MarketPlaces::Tiktok()->reverse(new FakeIntegration(
        accessToken: 'access',
        refreshToken: 'rt',
        settings: ['app_key' => 'ak', 'app_secret' => 'as', 'shop_cipher' => 'cipher1'],
        active: true,
        expired: false,
    ));
}

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function () {
    $payload = ttEligFixture();
    $perdidos = RecursiveFieldCoverage::missing(
        $payload,
        AftersaleEligibilityResponseDTO::fromArray($payload)->toArray(),
    );

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
});

it('faz roundtrip lossless do payload inteiro', function () {
    $payload = ttEligFixture();

    expect(AftersaleEligibilityResponseDTO::fromArray($payload)->toArray())->toEqual($payload);
});

// ─────────────────────────────────────────────────────────────────────────
// Comportamento real — o que surpreende quem consome
// ─────────────────────────────────────────────────────────────────────────

it('available_reason_names sobrevive como STRING mesmo a doc declarando []string', function () {
    $linha = AftersaleEligibilityResponseDTO::fromArray(ttEligFixture())
        ->skuEligibility[0]
        ->lineItemEligibility[0];

    // A doc diz []string, o exemplo oficial manda escalar. Tipar so' array
    // faria o valor sumir em silencio — exatamente a regressao que a lib caca.
    expect($linha->availableReasonNames)->toBe('available reason names');
});

it('eligible=true convive com codigo/motivo de INELEGIBILIDADE preenchidos', function () {
    $linha = AftersaleEligibilityResponseDTO::fromArray(ttEligFixture())
        ->skuEligibility[0]
        ->lineItemEligibility[0];

    // O exemplo oficial traz os dois: quem decidir por `ineligible_code != null`
    // em vez de por `eligible` bloqueia pos-venda valido.
    expect($linha->eligible)->toBeTrue()
        ->and($linha->ineligibleCode)->toBe(25001001)
        ->and($linha->ineligibleReason)->toBe('invalid order status');
});

it('a linha traz o id LEGADO e a lista nova com sub-item de kit', function () {
    $linha = AftersaleEligibilityResponseDTO::fromArray(ttEligFixture())
        ->skuEligibility[0]
        ->lineItemEligibility[0];

    // order_line_items_ids nao enderca componente de bundle; order_line_list sim.
    expect($linha->orderLineItemsIds)->toBe(['576469648086306000'])
        ->and($linha->orderLineList[0]->orderLineItemId)->toBe('576469648086306986')
        ->and($linha->orderLineList[0]->subOrderLineItemId)->toBe('576469648086306987');
});

it('getAftersaleEligibility usa GET na 202602 e manda request_types separado por virgula', function () {
    Http::fake([
        '*/return_refund/202602/orders/577087614418520388/aftersale_eligibility*' => Http::response([
            'code' => 0,
            'message' => 'Success',
            'data' => ttEligFixture(),
        ]),
    ]);

    $dto = ttEligReverse()->getAftersaleEligibility(
        '577087614418520388',
        initiateAftersaleUser: 'BUYER',
        requestTypes: ['REFUND', 'CANCEL'],
    );

    expect($dto->skuEligibility)->toHaveCount(1)
        ->and($dto->skuEligibility[0]->skuId)->toBe('1729385144994206632');

    Http::assertSent(fn ($r) => $r->method() === 'GET'
        && str_contains($r->url(), '/return_refund/202602/orders/577087614418520388/aftersale_eligibility')
        && str_contains($r->url(), 'initiate_aftersale_user=BUYER')
        // lista vira "REFUND,CANCEL" (virgula url-encoded), nunca request_types[0]=
        && str_contains(urldecode($r->url()), 'request_types=REFUND,CANCEL')
        && ! str_contains($r->url(), 'request_types%5B0%5D'));
});

it('omite os filtros opcionais da query quando nao informados', function () {
    Http::fake(['*aftersale_eligibility*' => Http::response(['code' => 0, 'data' => ttEligFixture()])]);

    ttEligReverse()->getAftersaleEligibility('577087614418520388');

    Http::assertSent(fn ($r) => ! str_contains($r->url(), 'initiate_aftersale_user')
        && ! str_contains($r->url(), 'request_types'));
});
