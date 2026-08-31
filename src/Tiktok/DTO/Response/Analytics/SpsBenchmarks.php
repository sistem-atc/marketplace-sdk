<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Limiares de faixa da metrica SPS. Mesmo shape em Get SPS Metrics e em
 * Get SPS Metric Diagnosis — DTO reusado.
 */
final class SpsBenchmarks implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $excellentThreshold = null,
        public readonly ?string $poorThreshold = null,
    ) {}
}
