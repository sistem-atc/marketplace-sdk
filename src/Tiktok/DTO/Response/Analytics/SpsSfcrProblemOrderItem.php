<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `sfcr_problem_order_items[]` — cancelamento por culpa do vendedor.
 * `cancellationReasons` e' []string na doc mas string no exemplo -> `mixed`.
 */
final class SpsSfcrProblemOrderItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public mixed $cancellationReasons = null,
        public readonly ?int $orderCreateTime = null,
        public readonly ?string $orderId = null,
        public readonly ?string $productId = null,
        public readonly ?string $productName = null,
        public readonly ?string $skuId = null,
    ) {}
}
