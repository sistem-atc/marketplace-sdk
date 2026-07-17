<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Inventory;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Referência externa do inventário (`external_references[]`) — liga o
 * inventory_id ao anúncio/variação (`{type:'item', id:'MLB…', variation_id}`).
 */
final class InventoryExternalReference implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $id = null,
        public readonly ?int $variationId = null,
    ) {}
}
