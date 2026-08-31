<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Diagnostico textual ja' localizado.
 *
 * `summaries`/`details` sao declarados []string na doc, mas o exemplo OFICIAL
 * manda string crua. `mixed` preserva os dois sem descartar valor.
 */
final class SpsMetricAnalysis implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public mixed $summaries = null,
        public mixed $details = null,
    ) {}
}
