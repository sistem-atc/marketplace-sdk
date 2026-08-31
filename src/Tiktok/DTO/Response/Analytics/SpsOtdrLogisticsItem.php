<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `otdr_logistics_items[]` — o furo de OTDR e' por TRANSPORTADORA, nao por
 * produto. `onTimeDeliverRate` e' fracao STRING 0..1.
 */
final class SpsOtdrLogisticsItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $deliveredOrderCount = null,
        public readonly ?string $logisticsProvider = null,
        public readonly ?int $onTimeDeliverOrderCount = null,
        public readonly ?string $onTimeDeliverRate = null,
    ) {}
}
