<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `total_performance` do produto na listagem: o TOTAL de todos os canais.
 *
 * Os pares `x` / `unique_x` medem a mesma coisa com deduplicacao por usuario
 * (`productImpressions` vs `uniqueProductImpressions`, `productClicks` vs
 * `uniqueClicks`, `addCartCount` vs `addCartUsers`) — nao some os dois.
 *
 * `gmvInclTax`/`tax`/`shippingFees` so' vem em MOEDA LOCAL, mesmo com currency=USD
 * na consulta. `grossMerchandiseValue` repete `gmv` (redundancia da propria API);
 * mantido porque descartar campo e' proibido.
 */
final class ShopProductTotalPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?int $orders = null,
        public readonly ?int $skuOrders = null,
        public readonly ?int $itemsSold = null,
        public readonly ?int $estimatedCustomers = null,
        public readonly ?MonetaryValue $aov = null,
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
        public readonly ?MonetaryValue $gmvInclTax = null,
        public readonly ?MonetaryValue $tax = null,
        public readonly ?MonetaryValue $grossMerchandiseValue = null,
        public readonly ?MonetaryValue $shippingFees = null,
        public readonly ?MonetaryValue $refunds = null,
        public readonly ?int $refundedItems = null,
        public readonly ?int $refundCustomers = null,
    ) {}
}
