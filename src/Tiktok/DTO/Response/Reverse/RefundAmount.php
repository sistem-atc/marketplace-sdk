<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Valores de reembolso. Serve `refund_amount` (return/cancel/line item) e o
 * `order_refund_amount` do Calculate Refund — a shape e' a mesma, o que muda e'
 * quais campos vem preenchidos.
 *
 * DINHEIRO E STRING no TikTok ("1.23", "0", "0.1"). Tipar float quebraria o
 * roundtrip e comeria a casa decimal — a doc manda "1" pro subtotal e "1.23"
 * pro total no MESMO objeto. Converta so' na hora da conta.
 *
 * ESTE E' O NUMERO QUE FECHA O TITULO no Contas a Receber: `refundTotal` e'
 * quanto sai da nossa mao, e ele NAO e' a soma ingenua dos itens — inclui
 * frete, imposto e taxas, e o `refundShippingFee` pode ser zero mesmo com o
 * comprador tendo pago frete (depende de quem cancelou).
 */
final class RefundAmount implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $currency = null,
        public readonly ?string $refundTotal = null,
        public readonly ?string $refundSubtotal = null,
        public readonly ?string $refundShippingFee = null,
        public readonly ?string $refundTax = null,
        /** So' EUA (Colorado): incide quando o GMV da plataforma passa de 500k USD. */
        public readonly ?string $retailDeliveryFee = null,
        /** So' mercado ID (Indonesia): taxa de servico cobrada do comprador. */
        public readonly ?string $buyerServiceFee = null,
    ) {}
}
