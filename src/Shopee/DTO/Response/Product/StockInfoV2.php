<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Estoque do anúncio/variação (`stock_info_v2`).
 *
 * `sellerStock` = estoque no vendedor; `shopeeStock` = estoque já no
 * armazém da Shopee (Full/FBS). São listas por localização.
 *
 * @property list<StockDetail>|null $sellerStock
 * @property list<StockDetail>|null $shopeeStock
 */
final class StockInfoV2 implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?StockSummary $summaryInfo = null,
        #[ArrayOf(StockDetail::class)]
        public readonly ?array $sellerStock = null,
        #[ArrayOf(StockDetail::class)]
        public readonly ?array $shopeeStock = null,
        public readonly ?AdvanceStock $advanceStock = null,
    ) {}
}
