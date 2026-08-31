<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto de colaboracao ABERTA — item de `products[]` do
 * /open_collaborations/products/search (202405) e do
 * /open_collaborations/products (202509).
 *
 * `unitsSold` some quando o creator nao autorizou compartilhamento preciso de
 * dados (ausencia != zero).
 *
 * @property list<CategoryChain>|null $categoryChains
 */
final class OpenCollaborationProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ShopInfo $shop = null,
        public readonly ?string $id = null,
        public readonly ?bool $hasInventory = null,
        public readonly ?int $unitsSold = null,
        public readonly ?string $title = null,
        public readonly ?string $saleRegion = null,
        public readonly ?string $mainImageUrl = null,
        public readonly ?string $detailLink = null,
        public readonly ?PriceRange $originalPrice = null,
        #[ArrayOf(CategoryChain::class)]
        public readonly ?array $categoryChains = null,
        public readonly ?ProductCommission $commission = null,
        public readonly ?PriceRange $salesPrice = null,
        // So no 202509 (busca por product_ids); a busca 202405 nao devolve.
        public readonly ?ShopAdsCommission $shopAdsCommission = null,
    ) {}
}
