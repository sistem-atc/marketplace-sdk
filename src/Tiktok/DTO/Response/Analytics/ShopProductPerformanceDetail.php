<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.performance` do /analytics/{v}/shop_products/{product_id}/performance —
 * o drill-down de UM produto.
 *
 * `ratings`, `topContents` e `topCreators` sao do PERIODO INTEIRO consultado, nao
 * por intervalo: nao os atribua a um `intervals[]` especifico.
 */
final class ShopProductPerformanceDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ShopProductDetailInterval::class)]
        public readonly ?array $intervals = null,
        #[ArrayOf(ShopProductRating::class)]
        public readonly ?array $ratings = null,
        #[ArrayOf(ShopProductTopContentGroup::class)]
        public readonly ?array $topContents = null,
        #[ArrayOf(ShopProductTopCreator::class)]
        public readonly ?array $topCreators = null,
    ) {}
}
