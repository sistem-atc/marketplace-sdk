<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Os tres niveis de preco da vitrine, cada um como faixa min/max:
 * cheio, com desconto do VENDEDOR e com desconto da PLATAFORMA.
 */
final class ShowcasePrice implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?PriceRange $originalPrice = null,
        public readonly ?PriceRange $sellerDiscountPrice = null,
        public readonly ?PriceRange $platformDiscountPrice = null,
    ) {}
}
