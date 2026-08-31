<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Como a metrica foi calculada: numerador/denominador com os rotulos ja'
 * localizados. Valores vem como STRING mesmo sendo contagens ("1000").
 */
final class SpsCalculationRule implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $denominatorLabel = null,
        public readonly ?string $denominatorValue = null,
        public readonly ?string $numeratorLabel = null,
        public readonly ?string $numeratorValue = null,
    ) {}
}
