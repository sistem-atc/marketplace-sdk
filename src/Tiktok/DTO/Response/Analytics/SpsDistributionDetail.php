<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Bucket de distribuicao da metrica. `percent` e' 0..100 (nao fracao) — o oposto
 * de `SpsMetricTrendDataPoint.value`, que segue o `valueUnit` da metrica.
 */
final class SpsDistributionDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $count = null,
        public readonly ?string $name = null,
        public readonly ?string $percent = null,
    ) {}
}
