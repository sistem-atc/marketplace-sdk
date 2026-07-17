<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Bloco de dimensões (`dimensions[]`) — altura/largura/comprimento/peso. */
final class Dimension implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?Measure $height = null,
        public readonly ?Measure $width = null,
        public readonly ?Measure $length = null,
        public readonly ?Measure $weight = null,
    ) {}
}
