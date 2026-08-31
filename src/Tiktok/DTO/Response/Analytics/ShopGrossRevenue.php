<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Receita bruta = (pagamento do cliente + subsidio de produto da plataforma) - impostos.
 * NAO e' o mesmo que GMV: GMV inclui cancelados e reembolsados e nao desconta imposto.
 */
final class ShopGrossRevenue implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?MonetaryValue $overall = null,
        #[ArrayOf(ShopGrossRevenueBreakdown::class)]
        public readonly ?array $breakdowns = null,
    ) {}
}
