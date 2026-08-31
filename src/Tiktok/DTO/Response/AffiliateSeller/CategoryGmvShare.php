<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Fatia do GMV do creator numa categoria de 1o nivel. `value` e' FRACAO em
 * string ("0.3035" = 30,35%), apesar de a doc dizer "centesimos de %".
 */
final class CategoryGmvShare implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $categoryId = null,
        // fracao: "0.3035" = 30,35%
        public readonly ?string $value = null,
    ) {}
}
