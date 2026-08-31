<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.product_stats[]` — desempenho de UM produto dentro de UMA live.
 *
 * `isLive` diz se o item ainda esta' em exibicao no momento da consulta.
 * `inventoryConsumptionCount` sao as unidades vendidas; `inventoryLeftCount` e'
 * o saldo restante. Taxas vem em fracao STRING.
 */
final class LiveRoomProductStat implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $mainImageUrl = null,
        public readonly ?string $productId = null,
        public readonly ?bool $isLive = null,
        public readonly ?string $clickThroughRate = null,
        public readonly ?string $sellableRegion = null,
        public readonly ?int $createdOrderCount = null,
        public readonly ?int $exposureCount = null,
        public readonly ?int $totalClickCount = null,
        public readonly ?MonetaryValue $localGmv = null,
        public readonly ?string $productName = null,
        public readonly ?MonetaryValue $localUnitPrice = null,
        public readonly ?int $paidOrderCount = null,
        public readonly ?int $inventoryLeftCount = null,
        public readonly ?int $inventoryConsumptionCount = null,
        public readonly ?int $createdOrderUserCount = null,
        public readonly ?int $paidUserCount = null,
        public readonly ?string $clickOrderRate = null,
    ) {}
}
