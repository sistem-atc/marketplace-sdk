<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Nota por dimensao do SPS. `weight` e' PERCENTUAL 0..100 ("70"), enquanto `score`
 * e' 0..5 — duas escalas diferentes no mesmo objeto.
 */
final class SpsDimension implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $score = null,
        public readonly ?string $status = null,
        public readonly ?string $statusText = null,
        public readonly ?string $weight = null,
    ) {}
}
