<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** `data.weight_recommendation` do webhook TYPE 62 — peso sugerido pro pacote. */
final class WeightRecommendation implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // GRAM
        public readonly ?string $unit = null,
        public readonly ?PackageMeasurementRange $weight = null,
    ) {}
}
