<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Payment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Uma transação da CARTEIRA Shopee — `transaction_list[]` de
 * /api/v2/payment/get_wallet_transaction_list. É o nível SETTLEMENT/payout: o
 * dinheiro que de fato entrou/saiu da carteira do vendedor (o repasse real),
 * vs o escrow POR PEDIDO (getEscrowDetail).
 *
 *   amount          = valor da transação (repasse liberado, saque, ajuste...)
 *   currentBalance  = saldo da carteira após a transação
 *   transactionType = tipo (ESCROW_VERIFIED_ADD = repasse liberado, WITHDRAWAL_CREATED = saque...)
 *   orderSn         = pedido de origem (quando aplicável)
 *
 * Dinheiro como ?float (nunca trunca — vide OrderIncome).
 */
final class WalletTransaction implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly int|string|null $transactionId = null,
        public readonly ?string $status = null,
        public readonly ?string $transactionType = null,
        public readonly ?float $amount = null,
        public readonly ?float $currentBalance = null,
        public readonly ?int $createTime = null,
        public readonly ?string $orderSn = null,
        public readonly ?string $reason = null,
        public readonly ?string $walletType = null,
        public readonly ?string $moneyFlow = null,
    ) {}
}
