<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `nrr_problem_order_items[]` — pedido com avaliacao negativa
 * (`userReviewRating` 1..5).
 */
final class SpsNrrProblemOrderItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $deliverTime = null,
        public readonly ?int $orderCreateTime = null,
        public readonly ?string $orderId = null,
        public readonly ?string $productId = null,
        public readonly ?string $productName = null,
        public readonly ?string $skuId = null,
        public readonly ?int $userReviewRating = null,
    ) {}
}
