<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Marketing;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Anúncio participante do desconto (`item_list[]`).
 *
 * Item com variação traz o preço promo por model em `modelList` — o
 * `itemPromotionPrice` do pai pode não refletir todas as variações.
 *
 * @property list<DiscountModel>|null $modelList
 */
final class DiscountItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $itemId = null,
        public readonly ?string $itemName = null,
        public readonly ?float $itemOriginalPrice = null,
        public readonly ?float $itemPromotionPrice = null,
        public readonly ?int $itemPromotionStock = null,
        public readonly ?int $normalStock = null,
        public readonly ?int $purchaseLimit = null,
        #[ArrayOf(DiscountModel::class)]
        public readonly ?array $modelList = null,
    ) {}
}
