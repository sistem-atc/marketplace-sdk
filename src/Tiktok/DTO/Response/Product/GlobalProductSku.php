<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * SKU do produto GLOBAL (`skus[]`).
 *
 * `globalQuantity` e' o total somado de todos os shops locais; ele so' pode
 * ser definido na 1a publicacao — depois disso a quantidade se ajusta por
 * shop local, e mandar esse campo de volta nao muda nada.
 *
 * `skuUnitCount` e `extraIdentifierCodes` sao exclusivos do mercado UE.
 *
 * @property list<GlobalSalesAttribute>|null $salesAttributes
 * @property list<GlobalSkuInventory>|null $inventory
 * @property list<string>|null $extraIdentifierCodes
 */
final class GlobalProductSku implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $sellerSku = null,
        public readonly ?GlobalSkuPrice $price = null,
        public readonly ?int $globalQuantity = null,
        public readonly ?GlobalIdentifierCode $identifierCode = null,
        public readonly ?string $skuUnitCount = null,
        public readonly ?string $externalGlobalSkuId = null,
        public readonly ?array $extraIdentifierCodes = null,
        #[ArrayOf(GlobalSalesAttribute::class)]
        public readonly ?array $salesAttributes = null,
        #[ArrayOf(GlobalSkuInventory::class)]
        public readonly ?array $inventory = null,
    ) {}
}
