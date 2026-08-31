<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.performance` do /analytics/{v}/shop_skus/{sku_id}/performance.
 *
 * Inconsistencia da propria doc: `productId` e' declarado INT e `skuId` STRING no
 * mesmo objeto. Seguimos o tipo declarado em cada um pra nao quebrar o roundtrip.
 */
final class ShopSkuPerformanceDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $productId = null,
        public readonly ?string $skuId = null,
        #[ArrayOf(ShopSkuDetailInterval::class)]
        public readonly ?array $intervals = null,
    ) {}
}
