<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Faixa recomendada (min/max) de peso ou dimensao.
 *
 * Numeros como STRING, igual dinheiro: "550", "19.5". Converta no consumidor.
 */
final class RecommendedRange implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $minValue = null,
        public readonly ?string $maxValue = null,
    ) {}
}
