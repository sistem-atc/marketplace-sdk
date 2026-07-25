<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance\Statement;

/**
 * Statement = um settlement/repasse REAL (data.statements[] de
 * GET /finance/{v}/statements). Dinheiro e' STRING (padrao TikTok).
 */
function fakeTiktokSettlementStatement(): array
{
    return [
        'id' => '7600000000000000000',
        'statement_time' => 1769000000,
        'settlement_amount' => '1234.56',
        'currency' => 'BRL',
        'revenue_amount' => '1500.00',
        'fee_amount' => '-265.44',
        'adjustment_amount' => '0.00',
        'net_sales_amount' => '1500.00',
        'payment_status' => 'PAID',
        'payment_id' => 'PAY-9988',
    ];
}

it('Statement hidrata os campos de settlement', function () {
    $s = Statement::fromArray(fakeTiktokSettlementStatement());

    expect($s->id)->toBe('7600000000000000000')
        ->and($s->settlementAmount)->toBe('1234.56')   // string, nao truncou casa
        ->and($s->currency)->toBe('BRL')
        ->and($s->feeAmount)->toBe('-265.44')
        ->and($s->paymentStatus)->toBe('PAID')
        ->and($s->paymentId)->toBe('PAY-9988');
});

it('Statement roundtrip preserva a shape snake_case', function () {
    $payload = fakeTiktokSettlementStatement();
    $back = Statement::fromArray($payload)->toArray();

    expect(data_get($back, 'settlement_amount'))->toBe('1234.56')
        ->and(data_get($back, 'payment_id'))->toBe('PAY-9988')
        ->and(data_get($back, 'statement_time'))->toBe(1769000000);
});
