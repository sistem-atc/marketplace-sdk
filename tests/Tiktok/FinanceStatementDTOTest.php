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

/**
 * Ajuste que o canal lança FORA do pedido — pacote perdido, avaria. Vem na
 * listagem por statement (nunca na do pedido) e traz o vínculo pronto em
 * `adjustment_order_id`.
 */
function fakeTiktokLogisticsReimbursement(): array
{
    return [
        'id' => '7667016574267557648',
        'type' => 'LOGISTICS_REIMBURSEMENT',
        'adjustment_id' => '7667014659456124679',
        'adjustment_order_id' => '585157474576139327',
        'adjustment_amount' => '30.9',
        'settlement_amount' => '30.9',
        'currency' => 'BRL',
        'order_create_time' => 1785116072,
    ];
}

it('hidrata o ajuste de logistica com o pedido que ele ressarce', function () {
    $t = StatementTransaction::fromArray(fakeTiktokLogisticsReimbursement());

    expect($t->type)->toBe('LOGISTICS_REIMBURSEMENT')
        ->and($t->adjustmentId)->toBe('7667014659456124679')
        ->and($t->adjustmentOrderId)->toBe('585157474576139327')
        ->and($t->settlementAmount)->toBe('30.9');
});

it('transacao de venda nao traz campos de ajuste', function () {
    // Sem isso, um ORDER poderia ser confundido com ajuste e creditar duas vezes.
    $t = StatementTransaction::fromArray(fakeTiktokStatement());

    expect($t->adjustmentOrderId)->toBeNull()
        ->and($t->adjustmentId)->toBeNull();
});

it('roundtrip nao perde os campos de ajuste', function () {
    $payload = fakeTiktokLogisticsReimbursement();
    $t = StatementTransaction::fromArray($payload);
    $back = $t->toArray();

    expect($back['type'])->toBe($payload['type'])
        ->and($back['adjustment_order_id'])->toBe($payload['adjustment_order_id'])
        ->and($back['adjustment_id'])->toBe($payload['adjustment_id']);
});

it('hidrata order_id quando vem do endpoint do demonstrativo e o devolve no toArray', function () {
    // /statements/{id}/statement_transactions traz order_id por transacao; o
    // endpoint por pedido nao (ja' e' o pedido da URL). Sem o campo, nao da'
    // pra ligar a transacao do demonstrativo ao pedido.
    $dto = StatementTransaction::fromArray(fakeTiktokStatement() + ['order_id' => '585390048990233842', 'type' => 'ORDER']);

    expect($dto->orderId)->toBe('585390048990233842')
        ->and($dto->toArray()['order_id'])->toBe('585390048990233842');

    expect(StatementTransaction::fromArray(fakeTiktokStatement())->orderId)->toBeNull();
});
