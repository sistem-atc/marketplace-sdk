<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Eixo de variação (`tier_variation[]`) — ex.: name="Peso",
 * optionList=["250g","500g","1Kg"]. A ORDEM importa: `Model::$tierIndex`
 * aponta por posição aqui dentro.
 *
 * @property list<TierOption>|null $optionList
 */
final class TierVariation implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $name = null,
        #[ArrayOf(TierOption::class)]
        public readonly ?array $optionList = null,
    ) {}
}
