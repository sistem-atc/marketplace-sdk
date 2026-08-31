<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Armazem elegivel pra um metodo de inbound.
 *
 * Aqui as chaves sao `warehouse_name`/`warehouse_id` — NAO `fbt_warehouse_id`
 * como no Get FBT Warehouse List. Sao ids do mesmo dominio FBT, mas com nome de
 * campo diferente por endpoint.
 */
final class FbtInboundMethodWarehouse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $warehouseName = null,
        public readonly ?string $warehouseId = null,
    ) {}
}
