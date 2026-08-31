<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Identificacao resumida do goods dentro de inventario/movimentacao.
 *
 * `skus` so' vem no Search FBT Inventory; no Search FBT Inventory Record o mesmo
 * objeto chega sem ele — por isso nullable, e nao lista vazia.
 *
 * @property list<FbtInventorySku>|null $skus
 */
final class FbtInventoryGoods implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        // Codigo do SELLER — chave do de/para com o SKU do ERP.
        public readonly ?string $referenceCode = null,
        public readonly ?string $name = null,
        #[ArrayOf(FbtInventorySku::class)]
        public readonly ?array $skus = null,
    ) {}
}
