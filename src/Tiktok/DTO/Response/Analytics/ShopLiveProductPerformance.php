<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.products[]` do /analytics/{v}/shop/{live_id}/products_performance —
 * desempenho por produto DENTRO de uma live especifica.
 *
 * Nao confunda com `ShopProductPerformanceSummary` (produto na loja inteira, por
 * canal) nem com `LiveRoomProductStat` (mesma ideia, mas na API de creator
 * live_rooms, com nomes de campo totalmente diferentes).
 */
final class ShopLiveProductPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?ShopLiveProductSales $sales = null,
        public readonly ?ShopLiveProductTraffic $traffic = null,
    ) {}
}
