<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do /analytics/{v}/live_rooms/{live_room_id}/user_portraits.
 *
 * Sao SETE listas do mesmo shape, separadas por dois eixos que e' facil confundir:
 * - prefixo `all*`  = toda a audiencia; prefixo `paid*` = so' trafego pago (ads)
 * - sufixo `*GenderIndicators` / `*AgeIndicators` / `*FanIndicators` = o eixo medido
 * `regionIndicators` (pais) so' existe na versao "all".
 */
final class LiveRoomUserPortraits implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(LiveRoomPortraitIndicator::class)]
        public readonly ?array $allAdsGenderIndicators = null,
        #[ArrayOf(LiveRoomPortraitIndicator::class)]
        public readonly ?array $allFanIndicators = null,
        #[ArrayOf(LiveRoomPortraitIndicator::class)]
        public readonly ?array $allAdsAgeIndicators = null,
        #[ArrayOf(LiveRoomPortraitIndicator::class)]
        public readonly ?array $regionIndicators = null,
        #[ArrayOf(LiveRoomPortraitIndicator::class)]
        public readonly ?array $paidAdsAgeIndicators = null,
        #[ArrayOf(LiveRoomPortraitIndicator::class)]
        public readonly ?array $paidAdsGenderIndicators = null,
        #[ArrayOf(LiveRoomPortraitIndicator::class)]
        public readonly ?array $paidFanIndicators = null,
    ) {}
}
