<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Comissao vigente da colaboracao aberta. `rate` em centesimos de por cento
 * (3000 = 30,00%), faixa [100, 8000].
 *
 * Numa colaboracao TERMINATING o `endTime` e' a hora em que ela expira.
 */
final class OpenCollaborationCommission implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // centesimos de %: 3000 = 30,00%
        public readonly ?int $rate = null,
        public readonly ?int $startTime = null,
        public readonly ?int $endTime = null,
    ) {}
}
