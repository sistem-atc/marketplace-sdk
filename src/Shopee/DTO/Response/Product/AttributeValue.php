<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Valor de atributo (`attribute_list[].attribute_value_list[]`). */
final class AttributeValue implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $valueId = null,
        public readonly ?string $originalValueName = null,
        public readonly ?string $valueUnit = null,
    ) {}
}
