<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Faixa de preco por SKU do produto na promocao do creator — min/max sao
 * objetos de dinheiro completos, nao escalares.
 */
final class CreatorPromotionPriceRange implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?AffiliateMoney $minPrice = null,
        public readonly ?AffiliateMoney $maxPrice = null,
    ) {}
}
