<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * De/para de SKU entre o produto global e o local publicado num mercado.
 * E' por aqui que se casa um pedido de shop local com o catalogo global.
 *
 * @property list<GlobalSalesAttributeMapping>|null $salesAttributeMappings
 */
final class GlobalSkuMapping implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $globalSkuId = null,
        public readonly ?string $localSkuId = null,
        #[ArrayOf(GlobalSalesAttributeMapping::class)]
        public readonly ?array $salesAttributeMappings = null,
    ) {}
}
