<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Armazém GLOBAL (cross-border) — item de `data.global_warehouses[]`.
 *
 * Não confundir com Warehouse: o global é do seller global (cross-border) e o
 * endpoint NÃO usa shop_cipher; o outro é por loja. `ownership` distingue
 * galpão do seller (SELLER) do galpão do TikTok (PLATFORM_COOPERATION).
 */
final class GlobalWarehouse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $ownership = null,
    ) {}
}
