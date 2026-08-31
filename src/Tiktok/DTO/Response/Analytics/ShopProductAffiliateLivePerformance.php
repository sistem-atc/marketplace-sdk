<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `affiliate_live_performance`: o produto nas lives de AFILIADOS.
 *
 * Duas armadilhas de nomenclatura em relacao aos canais da propria loja:
 * o GMV vem em `liveAttributedGmv` (e nao `attributedGmv`) e os usuarios que
 * adicionaram ao carrinho em `atcUsers` (e nao `addCartUsers`). Este canal
 * tambem NAO devolve orders/sku_orders/items_sold/customers/aov.
 */
final class ShopProductAffiliateLivePerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?MonetaryValue $liveAttributedGmv = null,
        public readonly ?int $newLiveCount = null,
        public readonly ?int $productImpressions = null,
        public readonly ?int $productClicks = null,
        public readonly ?string $ctr = null,
        public readonly ?int $addCartCount = null,
        public readonly ?string $addCartRate = null,
        public readonly ?string $clickOrderRate = null,
        public readonly ?int $uniqueProductImpressions = null,
        public readonly ?int $uniqueClicks = null,
        public readonly ?string $uniqueCtr = null,
        public readonly ?int $atcUsers = null,
        public readonly ?string $uniqueAtcRate = null,
        public readonly ?string $uniqueClickOrderRate = null,
    ) {}
}
