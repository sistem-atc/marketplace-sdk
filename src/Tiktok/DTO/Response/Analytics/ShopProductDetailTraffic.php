<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Trafego do produto no intervalo. So' existe detalhado por tipo de conteudo —
 * a API NAO devolve um total agregado aqui.
 */
final class ShopProductDetailTraffic implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ShopProductTrafficBreakdown::class)]
        public readonly ?array $breakdowns = null,
    ) {}
}
