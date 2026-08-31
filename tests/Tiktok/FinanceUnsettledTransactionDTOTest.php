<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance\UnsettledTransaction;

/**
 * Transacoes NAO liquidadas = o repasse que ainda vai cair. E' a unica fonte
 * pra provisionar o Contas a Receber antes do settlement — o statement so'
 * enxerga o que ja' foi apurado.
 */
function fixtureTiktokUnsettledData(): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/get-unsettled-transactions.json'),
        true,
    );
}

function fixtureTiktokUnsettledTransaction(): array
{
    return fixtureTiktokUnsettledData()['transactions'][0];
}

function ttUnsettledIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'access',
        refreshToken: 'rt',
        settings: [
            'app_key' => 'ak',
            'app_secret' => 'as',
            'shop_id' => 'shop1',
            'shop_cipher' => 'cipher1',
        ],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

it('estimatedSettlement e STRING porque pode vir como POLITICA em texto', function () {
    $t = UnsettledTransaction::fromArray(fixtureTiktokUnsettledTransaction());

    // Enquanto o pedido nao e' entregue o TikTok manda "x days after
    // delivery" no lugar do epoch. Tipar int transformaria o texto em 0 e o
    // Contas a Receber acharia que o repasse ja' venceu.
    expect($t->estimatedSettlement)->toBeString()
        ->and($t->estimatedSettlement)->toBe('1685548800')
        ->and($t->unsettledReason)->toBe('waiting for deliveery')
        // Ja' os tempos do pedido sao epoch em SEGUNDOS de verdade.
        ->and($t->orderCreateTime)->toBe(1685548800)
        ->and($t->orderDeliveryTime)->toBe(1685548800);
});

it('mantem dinheiro como STRING com sinal — desconto ja vem negativo', function () {
    $t = UnsettledTransaction::fromArray(fixtureTiktokUnsettledTransaction());

    expect($t->estSettlementAmount)->toBe('130')
        ->and($t->estRevenueAmount)->toBe('200')
        // Negativo e' cobranca. Aplicar abs() aqui inverteria o repasse.
        ->and($t->estFeeTaxAmount)->toBe('-30')
        ->and($t->estShippingCostAmount)->toBe('-70')
        ->and($t->revenueBreakdown->sellerDiscountAmount)->toBe('-10')
        ->and($t->feeTaxBreakdown->fee->affiliateCommissionAmount)->toBe('-4');
});

it('o supplementary_component do frete e informativo, nao soma', function () {
    $t = UnsettledTransaction::fromArray(fixtureTiktokUnsettledTransaction());

    $sup = $t->shippingCostBreakdown->supplementaryComponent;

    // Estes valores ja' estao DENTRO de actual_shipping_fee_amount /
    // shipping_fee_discount_amount. Somar junto duplicaria o frete.
    expect($sup->fbtFulfillmentFeeAmount)->toBe('-50')
        ->and($sup->promoShippingIncentiveAmount)->toBe('30')
        ->and($sup->customerShippingFeeOffsetAmount)->toBe('-6.99')
        ->and($t->shippingCostBreakdown->actualShippingFeeAmount)->toBe('-50');
});

it('separa venda de ajuste — cada transacao tem OU order_id OU adjustment_id', function () {
    $t = UnsettledTransaction::fromArray(fixtureTiktokUnsettledTransaction());

    expect($t->type)->toBe('ORDER')
        ->and($t->status)->toBe('UNSETTLED')
        // O ajuste (CHARGE_BACK, LOGISTICS_REIMBURSEMENT, PLATFORM_PENALTY...)
        // liga no pedido ressarcido por adjustment_order_id — sem esse campo
        // nao ha como amarrar o ajuste ao titulo.
        ->and($t->adjustmentId)->toBe('7238804564097517332')
        ->and($t->adjustmentOrderId)->toBe('576463220456522968')
        ->and($t->orderId)->toBe('576463220456522968');
});

it('nao descarta NENHUM campo do exemplo OFICIAL da doc (inclusive aninhados)', function () {
    $payload = fixtureTiktokUnsettledTransaction();
    $back = UnsettledTransaction::fromArray($payload)->toArray();

    $perdidos = array_diff(array_keys($payload), array_keys($back));
    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));

    // Roundtrip PROFUNDO: o bug original passava no teste raso porque o campo
    // perdido morava num objeto aninhado.
    expect($back)->toEqual($payload);
});

it('getUnsettledTransactions preserva os totais da consulta inteira', function () {
    $data = fixtureTiktokUnsettledData();

    Http::fake([
        'open-api.tiktokglobalshop.com/finance/202507/orders/unsettled*' => Http::response([
            'code' => 0,
            'message' => 'Success',
            'data' => $data,
        ]),
    ]);

    $resp = MarketPlaces::Tiktok()->finance(ttUnsettledIntegration())->getUnsettledTransactions();

    expect($resp['transactions'])->toHaveCount(1)
        ->and($resp['transactions'][0])->toBeInstanceOf(UnsettledTransaction::class)
        ->and($resp['total_count'])->toBe(2);

    // Os sum_* sao da consulta TODA, nao da pagina: nenhum deles pode sumir
    // no caminho, senao o provisionamento tem que recalcular na mao.
    $perdidos = array_diff(array_keys($data), array_keys($resp));
    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));

    expect($resp['sum_est_settlement_amount'])->toBe('100')
        ->and($resp['sum_est_fee_amount'])->toBe('-10');

    // sort_field e' obrigatorio; a janela usa search_time_*, nao create_time.
    Http::assertSent(fn ($req) => str_contains($req->url(), 'sort_field=order_create_time'));
});
