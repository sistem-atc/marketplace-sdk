<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Armazém do seller — item de `data.warehouses[]` de /logistics/202309/warehouses.
 *
 * `id` (snowflake STRING) é a chave usada em toda logística; `entityId` é o
 * endereço FÍSICO e pode repetir entre armazéns diferentes — o mesmo galpão
 * aparece 2× (SALES_WAREHOUSE e RETURN_WAREHOUSE) com ids distintos e o mesmo
 * entity_id. Não deduplique por endereço.
 *
 * `effectStatus` RESTRICTED não é erro: é modo férias ou limite de pedidos.
 */
final class Warehouse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $entityId = null,
        public readonly ?string $name = null,
        // ENABLED | DISABLED | RESTRICTED
        public readonly ?string $effectStatus = null,
        // SALES_WAREHOUSE | RETURN_WAREHOUSE
        public readonly ?string $type = null,
        // DOMESTIC_WAREHOUSE | CB_OVERSEA_WAREHOUSE | CB_DIRECT_SHIPPING_WAREHOUSE
        public readonly ?string $subType = null,
        public readonly ?bool $isDefault = null,
        public readonly ?WarehouseAddress $address = null,
    ) {}
}
