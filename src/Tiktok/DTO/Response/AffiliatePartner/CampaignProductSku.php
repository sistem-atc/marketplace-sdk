<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** SKU (variação) de um produto da campanha. */
final class CampaignProductSku implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $skuName = null,
        public readonly ?string $skuId = null,
        public readonly ?SkuInventory $inventory = null,
        /** Preço na região-base; as demais vêm em `regionPrices`. */
        public readonly ?SkuRegionPrice $basePrice = null,
        #[ArrayOf(SkuRegionPrice::class)]
        public readonly ?array $regionPrices = null,
        #[ArrayOf(SkuProperty::class)]
        public readonly ?array $properties = null,
    ) {}
}
