<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `revenue_breakdown` da transacao NAO liquidada: as parcelas que somam
 * exatamente o `estRevenueAmount`.
 *
 * Cuidado com o SINAL: o TikTok ja' manda desconto e devolucao NEGATIVOS
 * ("-10"), entao a conferencia e' SOMA de tudo, nunca subtracao.
 *
 * DINHEIRO E STRING (padrao TikTok).
 */
final class UnsettledRevenueBreakdown implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // Bruto do pedido antes de qualquer desconto (= gross sales da loja).
        public readonly ?string $subtotalBeforeDiscountAmount = null,
        public readonly ?string $sellerDiscountAmount = null,
        public readonly ?string $refundSubtotalBeforeDiscountAmount = null,
        public readonly ?string $sellerDiscountRefundAmount = null,
        // COD (cash on delivery): so' Arabia Saudita. No BR vem "0".
        public readonly ?string $codServiceFeeAmount = null,
        public readonly ?string $refundCodServiceFeeAmount = null,
        // Taxa de distancia (programa Horizon+, Indonesia).
        public readonly ?string $distantItemFeeAmount = null,
    ) {}
}
