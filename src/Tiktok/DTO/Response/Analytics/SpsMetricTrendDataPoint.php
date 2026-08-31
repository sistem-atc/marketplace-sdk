<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Ponto da serie historica da metrica. `recordDate` NAO e' epoch nem ISO: e'
 * string yyyyMMdd ("20260601").
 */
final class SpsMetricTrendDataPoint implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $recordDate = null,
        public readonly ?string $value = null,
    ) {}
}
