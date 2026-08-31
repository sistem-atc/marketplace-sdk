<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `fee_tax_breakdown` — a unica parte do payload de transacao nao liquidada
 * com DOIS niveis de aninhamento: taxa e imposto vem em objetos separados
 * (`fee` e `tax`), e nao achatados como no StatementTransaction ja' liquidado.
 *
 * Somados dao o `estFeeTaxAmount`.
 */
final class UnsettledFeeTaxBreakdown implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?UnsettledFee $fee = null,
        public readonly ?UnsettledTax $tax = null,
    ) {}
}
