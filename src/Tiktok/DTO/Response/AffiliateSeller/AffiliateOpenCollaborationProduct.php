<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto elegivel a colaboracao aberta na busca do seller.
 *
 * `hasInventory` e' booleano (tem/nao tem), NAO a quantidade.
 */
final class AffiliateOpenCollaborationProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?AffiliateProductShop $shop = null,
        public readonly ?string $id = null,
        public readonly ?bool $hasInventory = null,
        public readonly ?int $unitsSold = null,
        public readonly ?string $title = null,
        public readonly ?string $saleRegion = null,
        public readonly ?string $mainImageUrl = null,
        public readonly ?string $detailLink = null,
        public readonly ?AffiliatePriceRange $originalPrice = null,
        #[ArrayOf(AffiliateProductCategory::class)]
        public readonly ?array $categoryChains = null,
        public readonly ?AffiliateProductCommission $commission = null,
        // preco promocional min/max
        public readonly ?AffiliatePriceRange $salesPrice = null,
    ) {}
}
