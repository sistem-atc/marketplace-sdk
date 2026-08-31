<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * SKU LOCAL recem-criado pela publicacao, com o vinculo de volta pro global.
 *
 * A chave e' `sale_attributes` (singular "sale") — a API e' inconsistente:
 * no produto global o mesmo campo e' `sales_attributes`.
 *
 * @property list<GlobalCreatedSalesAttribute>|null $saleAttributes
 */
final class GlobalPublishedSku implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $relatedGlobalSkuId = null,
        public readonly ?string $sellerSku = null,
        #[ArrayOf(GlobalCreatedSalesAttribute::class)]
        public readonly ?array $saleAttributes = null,
    ) {}
}
