<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `GET /fbt/202602/inbound_orders`. Lote de ate' 10 ordens.
 *
 * @property list<FbtInboundOrder>|null $inboundOrders
 */
final class FbtInboundOrderDetailResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FbtInboundOrder::class)]
        public readonly ?array $inboundOrders = null,
    ) {}
}
