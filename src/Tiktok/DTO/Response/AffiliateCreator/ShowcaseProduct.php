<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto na VITRINE do creator — item de `products[]` do
 * /affiliate_creator/202405/showcases/products.
 *
 * `source`: THIRD_PARTY | AFFILIATE | TIKTOK_STORE. `thirdPartyLink` so' vem
 * quando o produto e' de fora do TikTok Shop.
 *
 * @property list<ShowcaseImage>|null $mainImages
 * @property list<string>|null $saleRegions
 */
final class ShowcaseProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?ShopInfo $shop = null,
        public readonly ?ShowcaseProductAddition $addition = null,
        public readonly ?ShowcasePrice $price = null,
        public readonly ?string $title = null,
        #[ArrayOf(ShowcaseImage::class)]
        public readonly ?array $mainImages = null,
        public readonly ?ShowcaseProductStatus $status = null,
        public readonly ?string $source = null,
        public readonly ?string $detailLink = null,
        public readonly ?string $thirdPartyLink = null,
        public readonly ?array $saleRegions = null,
        public readonly ?ShowcaseCommission $commission = null,
        public readonly ?ShowcaseCollaboration $collaboration = null,
    ) {}
}
