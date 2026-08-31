<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto da loja visto pelo creator — item de `products[]` do
 * /affiliate_creator/202509/shop_products.
 *
 * `addedStatus` (ADDABLE | ADDED | REJECTED) diz se da' pra jogar na vitrine.
 * O exemplo oficial vem com \n colado no valor ("ADDABLE\n") — normalize com
 * trim() antes de comparar.
 *
 * @property list<ProductImage>|null $images
 */
final class ShopProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        public readonly ?MoneyAmount $price = null,
        public readonly ?string $addedStatus = null,
        public readonly ?string $brandName = null,
        #[ArrayOf(ProductImage::class)]
        public readonly ?array $images = null,
        public readonly ?int $salesCount = null,
    ) {}
}
