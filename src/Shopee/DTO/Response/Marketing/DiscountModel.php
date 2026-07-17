<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Marketing;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Variação participante do desconto (`item_list[].model_list[]`). */
final class DiscountModel implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $modelId = null,
        public readonly ?string $modelName = null,
        public readonly ?float $modelOriginalPrice = null,
        public readonly ?float $modelPromotionPrice = null,
        public readonly ?int $modelPromotionStock = null,
        public readonly ?int $modelNormalStock = null,
    ) {}
}
