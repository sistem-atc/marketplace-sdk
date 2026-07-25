<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Shopee\DTO\Response\Payment\WalletTransaction;

/**
 * WalletTransaction = movimento da carteira (transaction_list[] de
 * /api/v2/payment/get_wallet_transaction_list) — o repasse real (nivel payout).
 * Dinheiro como ?float (nunca trunca).
 */
function fakeShopeeWalletTransaction(): array
{
    return [
        'transaction_id' => 987654321,
        'status' => 'COMPLETED',
        'transaction_type' => 'ESCROW_VERIFIED_ADD',
        'amount' => 88.75,
        'current_balance' => 1088.75,
        'create_time' => 1769000123,
        'order_sn' => '2509ABCDEF1234',
        'reason' => 'Order released',
        'money_flow' => 'MONEY_IN',
    ];
}

it('WalletTransaction hidrata o repasse liberado', function () {
    $t = WalletTransaction::fromArray(fakeShopeeWalletTransaction());

    expect($t->transactionId)->toBe(987654321)
        ->and($t->transactionType)->toBe('ESCROW_VERIFIED_ADD')
        ->and($t->amount)->toBe(88.75)
        ->and($t->currentBalance)->toBe(1088.75)
        ->and($t->orderSn)->toBe('2509ABCDEF1234')
        ->and($t->moneyFlow)->toBe('MONEY_IN');
});

it('WalletTransaction roundtrip preserva a shape snake_case', function () {
    $payload = fakeShopeeWalletTransaction();
    $back = WalletTransaction::fromArray($payload)->toArray();

    expect(data_get($back, 'transaction_type'))->toBe('ESCROW_VERIFIED_ADD')
        ->and(data_get($back, 'amount'))->toBe(88.75)
        ->and(data_get($back, 'order_sn'))->toBe('2509ABCDEF1234');
});
