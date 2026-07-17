<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance\StatementTransaction;

/**
 * Payload SINTETICO na shape de /finance/{v}/orders/{id}/statement_transactions.
 * Dinheiro e STRING (padrao TikTok); quantity e statement_time sao INT.
 */
function fakeTiktokStatement(): array
{
    return [
        'id' => '7500000000000000000',
        'currency' => 'BRL',
        'statement_time' => 1768953600,   // epoch INT, nao string
        'revenue_amount' => '97.48',
        'fee_amount' => '-18.30',
        'settlement_amount' => '79.18',
        'platform_commission_amount' => '-12.00',
        'referral_fee_amount' => '-4.30',
        'transaction_fee_amount' => '-2.00',
        'customer_order_refund_amount' => '0.00',
        'adjustment_amount' => '0.00',
        'sku_statement_transactions' => [
            [
                'id' => '7500000000000000001',
                'sku_id' => '1729592094930831000',
                'sku_name' => '300g',
                'product_name' => 'Creatina 300g',
                'quantity' => 2,             // INT
                'currency' => 'BRL',
                'revenue_amount' => '97.48',
                'settlement_amount' => '79.18',
                'platform_commission_amount' => '-12.00',
            ],
        ],
    ];
}

it('hidrata a transacao com dinheiro STRING e settlement', function () {
    $dto = StatementTransaction::fromArray(fakeTiktokStatement());

    expect($dto->revenueAmount)->toBe('97.48')      // string, casa decimal intacta
        ->and($dto->feeAmount)->toBe('-18.30')
        ->and($dto->settlementAmount)->toBe('79.18')
        ->and($dto->platformCommissionAmount)->toBe('-12.00');

    // Consumidor converte pra somar:
    expect((float) $dto->settlementAmount)->toBe(79.18);
});

it('mantem statement_time e quantity como INT, nao string', function () {
    $dto = StatementTransaction::fromArray(fakeTiktokStatement());

    expect($dto->statementTime)->toBe(1768953600)
        ->and($dto->skuStatementTransactions[0]->quantity)->toBe(2);
});

it('quebra por SKU em sku_statement_transactions', function () {
    $dto = StatementTransaction::fromArray(fakeTiktokStatement());

    expect($dto->skuStatementTransactions)->toHaveCount(1)
        ->and($dto->skuStatementTransactions[0]->skuId)->toBe('1729592094930831000')
        ->and($dto->skuStatementTransactions[0]->revenueAmount)->toBe('97.48');
});

it('faz roundtrip lossless do statement', function () {
    $payload = fakeTiktokStatement();

    expect(StatementTransaction::fromArray($payload)->toArray())->toEqual($payload);
});

it('preserva "0.00" como string — nao vira "0"', function () {
    // O motivo de dinheiro ser string: float faria "0.00" -> 0 -> "0".
    $dto = StatementTransaction::fromArray(['adjustment_amount' => '0.00']);

    expect($dto->adjustmentAmount)->toBe('0.00')
        ->and($dto->toArray()['adjustment_amount'])->toBe('0.00');
});
