<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `aht_problem_order_items[]` — pos-venda que estourou o tempo de
 * tratativa. Duracoes em HORAS, como STRING ("42.5").
 */
final class SpsAhtProblemOrderItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $aftersalesTypeName = null,
        public readonly ?string $approveDurationHours = null,
        public readonly ?string $inspectDurationHours = null,
        public readonly ?string $orderId = null,
        public readonly ?string $returnOrderId = null,
        public readonly ?int $reviewCount = null,
        public readonly ?string $totalHandleDurationHours = null,
    ) {}
}
