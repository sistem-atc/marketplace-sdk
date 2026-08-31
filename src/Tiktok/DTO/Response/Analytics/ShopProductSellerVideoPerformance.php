<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `seller_video_performance`: o produto nos videos DA PROPRIA LOJA.
 * Identico ao canal de live exceto por `newVideoCount` (em vez de `newLiveCount`) —
 * DTOs separados de proposito, pra nao dar pra trocar um pelo outro sem perceber.
 */
final class ShopProductSellerVideoPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?MonetaryValue $attributedGmv = null,
        public readonly ?int $attributedOrders = null,
        public readonly ?int $attributedSkuOrders = null,
        public readonly ?int $attributedSoldItems = null,
        public readonly ?int $estimatedCustomers = null,
        public readonly ?MonetaryValue $aov = null,
        public readonly ?int $newVideoCount = null,
        public readonly ?int $productImpressions = null,
        public readonly ?int $productClicks = null,
        public readonly ?string $ctr = null,
        public readonly ?int $addCartCount = null,
        public readonly ?string $addCartRate = null,
        public readonly ?string $clickOrderRate = null,
        public readonly ?int $uniqueProductImpressions = null,
        public readonly ?int $uniqueClicks = null,
        public readonly ?string $uniqueCtr = null,
        public readonly ?int $addCartUsers = null,
        public readonly ?string $uniqueAtcRate = null,
        public readonly ?string $uniqueClickOrderRate = null,
    ) {}
}
