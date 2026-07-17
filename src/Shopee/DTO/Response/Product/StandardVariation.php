<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Variação PADRONIZADA da Shopee (`standardise_tier_variation[]`) — o
 * catálogo normalizado dela, com ids próprios. Convive com `tierVariation`
 * (texto livre do vendedor); as duas descrevem os mesmos eixos.
 *
 * @property list<StandardVariationOption>|null $variationOptionList
 */
final class StandardVariation implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $variationId = null,
        public readonly ?string $variationName = null,
        #[ArrayOf(StandardVariationOption::class)]
        public readonly ?array $variationOptionList = null,
    ) {}
}
