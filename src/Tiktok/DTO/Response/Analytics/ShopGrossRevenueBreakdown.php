<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Fatia de receita bruta. So' vem PERCENTUAL (fracao STRING), nunca valor.
 * Os tipos mudam por mercado: SEA/MX/BR -> NON_GMV_MAX | GMV_MAX;
 * UK/EU -> ADS | ORGANIC; US/JP nao tem breakdown.
 */
final class ShopGrossRevenueBreakdown implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $percentage = null,
        public readonly ?string $type = null,
    ) {}
}
