<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Dimensões da embalagem (`dimension`) — em cm. */
final class Dimension implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $packageLength = null,
        public readonly ?int $packageWidth = null,
        public readonly ?int $packageHeight = null,
    ) {}
}
