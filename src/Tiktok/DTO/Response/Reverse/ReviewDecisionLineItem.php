<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Linha de devolucao com as decisoes candidatas pra ela (Get Review Decision).
 *
 * A elegibilidade e' POR LINHA: numa devolucao com varios itens, aprovar um e
 * rejeitar outro e' legitimo.
 *
 * @property list<Decision>|null $decisions
 */
final class ReviewDecisionLineItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $returnLineItemId = null,
        public readonly ?string $subReturnLineItemId = null,
        #[ArrayOf(Decision::class)]
        public readonly ?array $decisions = null,
    ) {}
}
