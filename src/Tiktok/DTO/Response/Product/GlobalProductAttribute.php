<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Atributo geral do produto global (fabricante, pais de origem, material...)
 * — descreve o produto todo, independente de variante.
 *
 * @property list<GlobalAttributeValue>|null $values
 */
final class GlobalProductAttribute implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        #[ArrayOf(GlobalAttributeValue::class)]
        public readonly ?array $values = null,
    ) {}
}
