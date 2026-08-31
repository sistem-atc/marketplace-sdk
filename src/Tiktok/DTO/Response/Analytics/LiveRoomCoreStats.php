<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.stats` do /analytics/{v}/live_rooms/{live_room_id}/core_stats — o
 * consolidado de UMA transmissao (nao da loja: pra loja veja `ShopLivePerformance*`).
 *
 * Cuidado com as tres contagens de pedido, que parecem sinonimos e nao sao:
 * - `createdOrderCount`  — SKU orders CRIADOS na live (inclui nao pagos)
 * - `paidOrderCount`     — SKU orders criados E PAGOS
 * - `buyerCount`         — usuarios UNICOS que pagaram (inclui devolvidos/reembolsados)
 *
 * Taxas (`clickThroughRate`, `clickOrderRate`) vem como STRING em fracao ("0.11"
 * = 11%), nao percentual. `avgWatchingDuration` e' em segundos.
 */
final class LiveRoomCoreStats implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // Unidades de produto vendidas na live.
        public readonly ?int $sales = null,
        public readonly ?MonetaryValue $localGmv = null,
        public readonly ?int $createdOrderCount = null,
        public readonly ?int $currentVisitorCount = null,
        public readonly ?int $paidOrderCount = null,
        public readonly ?MonetaryValue $localUnitPrice = null,
        // Cliques em produto (lista + card).
        public readonly ?int $productReachCount = null,
        public readonly ?int $watchPv = null,
        public readonly ?string $clickThroughRate = null,
        public readonly ?int $accumulatedNewFollowerCount = null,
        public readonly ?int $buyerCount = null,
        public readonly ?int $accumulatedCommentCount = null,
        // Impressoes de produto (lista + card).
        public readonly ?int $productViewCount = null,
        public readonly ?string $clickOrderRate = null,
        public readonly ?int $avgWatchingDuration = null,
        public readonly ?int $accumulatedSharingCount = null,
        public readonly ?int $peakConcurrentUserCount = null,
    ) {}
}
