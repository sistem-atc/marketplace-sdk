<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * SKU de um produto (`skus[]`).
 *
 * No SEARCH vem enxuto (id, seller_sku, price, inventory). No GET vem completo
 * (+ dimensões, peso, identifier_code, sales_attributes, status_info, política
 * de listing global). `sellerSku` é o de/para com o catálogo interno; IDs são
 * STRING (snowflake).
 *
 * @property list<SkuInventory>|null $inventory
 * @property list<SalesAttribute>|null $salesAttributes
 */
final class ProductSku implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $sellerSku = null,
        public readonly ?SkuPrice $price = null,
        #[ArrayOf(SkuInventory::class)]
        public readonly ?array $inventory = null,
        // Só no GET:
        public readonly ?SkuIdentifierCode $identifierCode = null,
        public readonly ?Dimensions $skuDimensions = null,
        public readonly ?Weight $skuWeight = null,
        public readonly ?SkuStatusInfo $statusInfo = null,
        public readonly ?SkuGlobalListingPolicy $globalListingPolicy = null,
        #[ArrayOf(SalesAttribute::class)]
        public readonly ?array $salesAttributes = null,
    ) {}
}
