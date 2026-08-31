<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.dimension_recommendation` do webhook TYPE 62 — dimensoes sugeridas pro
 * pacote. Uma faixa por eixo, todas na mesma `unit`.
 */
final class DimensionRecommendation implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // CENTIMETER
        public readonly ?string $unit = null,
        public readonly ?PackageMeasurementRange $length = null,
        public readonly ?PackageMeasurementRange $width = null,
        public readonly ?PackageMeasurementRange $height = null,
    ) {}
}
