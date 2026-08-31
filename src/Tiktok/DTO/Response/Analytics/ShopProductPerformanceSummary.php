<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.products[]` do Get Shop Product Performance List — UM produto com
 * o desempenho quebrado por OITO canais.
 *
 * Cada canal tem shape proprio (nomes de GMV e de carrinho mudam entre eles), por
 * isso sao oito DTOs distintos e nao um generico: trocar um pelo outro seria um
 * bug silencioso.
 */
final class ShopProductPerformanceSummary implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?ShopProductTotalPerformance $totalPerformance = null,
        public readonly ?ShopProductSellerLivePerformance $sellerLivePerformance = null,
        public readonly ?ShopProductSellerVideoPerformance $sellerVideoPerformance = null,
        public readonly ?ShopProductSellerProductCardPerformance $sellerProductCardPerformance = null,
        public readonly ?ShopProductAffiliateTotalPerformance $affiliateTotalPerformance = null,
        public readonly ?ShopProductAffiliateLivePerformance $affiliateLivePerformance = null,
        public readonly ?ShopProductAffiliateVideoPerformance $affiliateVideoPerformance = null,
        public readonly ?ShopProductShopTabPerformance $shopTabPerformance = null,
    ) {}
}
