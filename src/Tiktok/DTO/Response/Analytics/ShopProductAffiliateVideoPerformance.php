<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `affiliate_video_performance`: o produto nos videos de AFILIADOS.
 * GMV em `attributedVideoGmv` e carrinho em `atcUsers` — mesmas armadilhas do
 * canal de live afiliada.
 */
final class ShopProductAffiliateVideoPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?MonetaryValue $attributedVideoGmv = null,
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
        public readonly ?int $atcUsers = null,
        public readonly ?string $uniqueAtcRate = null,
        public readonly ?string $uniqueClickOrderRate = null,
    ) {}
}
