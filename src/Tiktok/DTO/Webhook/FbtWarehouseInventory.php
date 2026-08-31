<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Item de `fbt_warehouse_inventory` do webhook type 24: saldo por armazem. */
final class FbtWarehouseInventory implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $fbtWarehouseId = null,
        public readonly ?FbtOnHandDetail $onHandDetail = null,
    ) {}
}
