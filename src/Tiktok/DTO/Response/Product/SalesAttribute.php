<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Atributo de venda/variação do SKU (`skus[].sales_attributes[]`) — ex.:
 * Sabor=Chocolate. Vazio no corpus atual; a shape completa (id/name/value_id/
 * value_name/sku_img) é passthrough até aparecer.
 */
final class SalesAttribute implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $valueId = null,
        public readonly ?string $valueName = null,
    ) {}
}
