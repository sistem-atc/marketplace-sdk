<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Atributo de anúncio — shape que o ML repete em `attributes[]`, `sale_terms[]`
 * e `variations[].attribute_combinations[]`.
 *
 * `values` (lista) e `value_struct` (objeto, só em sale_terms) ficam crus: o ML
 * varia a forma conforme o value_type.
 *
 * @property array<int|string, mixed> $values
 */
final class ItemAttribute implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int|string, mixed> $values */
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $valueId = null,
        public readonly ?string $valueName = null,
        public readonly ?string $valueType = null,
        public readonly array $values = [],
        public readonly mixed $valueStruct = null,
        public readonly mixed $attributeGroupId = null,
        public readonly mixed $attributeGroupName = null,
    ) {}
}
