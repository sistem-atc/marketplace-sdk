<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Fatia de faixa etaria da audiencia do video. `age` e' STRING de faixa
 * ("18-24", "25-34", "35-44", "45-54", "55+"), nao numero.
 */
final class ShopVideoAgeDistribution implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $age = null,
        public readonly ?string $percentage = null,
    ) {}
}
