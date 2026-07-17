<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Atributo do produto (`product_attributes[]`) — alimenta o listing_attributes
 * keyed-by-MP do PIM. Chaveado por NOME (`name`); os valores vêm em `values[]`
 * (múltiplos viram lista).
 *
 * @property list<AttributeValue>|null $values
 */
final class ProductAttribute implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        #[ArrayOf(AttributeValue::class)]
        public readonly ?array $values = null,
    ) {}
}
