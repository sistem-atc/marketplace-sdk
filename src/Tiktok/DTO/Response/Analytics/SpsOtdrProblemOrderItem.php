<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `otdr_problem_order_items[]` — entrega fora do prazo. O atraso e' a
 * diferenca entre `actualDeliverTime` e `expectDeliverTime` (epoch em SEGUNDOS).
 */
final class SpsOtdrProblemOrderItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $actualDeliverTime = null,
        public readonly ?int $expectDeliverTime = null,
        public readonly ?string $orderId = null,
        public readonly ?string $productName = null,
        public readonly ?string $skuId = null,
    ) {}
}
