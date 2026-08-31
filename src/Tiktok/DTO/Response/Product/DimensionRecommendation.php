<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Correcao de dimensoes sugerida. `unit` e' fixo CENTIMETER. */
final class DimensionRecommendation implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $unit = null,
        public readonly ?RecommendedRange $length = null,
        public readonly ?RecommendedRange $height = null,
        public readonly ?RecommendedRange $width = null,
    ) {}
}
