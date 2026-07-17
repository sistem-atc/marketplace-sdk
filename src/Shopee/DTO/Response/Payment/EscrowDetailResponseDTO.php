<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Payment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/api/v2/payment/get_escrow_detail` — a fonte das TAXAS e do
 * REPASSE do pedido Shopee.
 *
 * `orderIncome->escrowAmount` = líquido que a Shopee paga.
 * `buyerPaymentInfo->buyerTotalAmount` = o que o comprador pagou.
 * A diferença são as taxas (comissão, serviço, transação, frete...).
 *
 * @property list<string>|null $returnOrderSnList
 */
final class EscrowDetailResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderSn = null,
        public readonly ?string $buyerUserName = null,
        public readonly ?OrderIncome $orderIncome = null,
        public readonly ?BuyerPaymentInfo $buyerPaymentInfo = null,
        // Lista de STRINGS (order_sn de devolucoes ligadas a este pedido).
        public readonly ?array $returnOrderSnList = null,
    ) {}
}
