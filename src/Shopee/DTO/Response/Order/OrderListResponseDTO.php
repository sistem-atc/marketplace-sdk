<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/api/v2/order/get_order_list` — paginação por CURSOR
 * (não por offset como o ML): pagine enquanto `more` for true, passando
 * `nextCursor` na chamada seguinte.
 *
 * @property list<OrderSummary>|null $orderList
 */
final class OrderListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(OrderSummary::class)]
        public readonly ?array $orderList = null,
        public readonly ?bool $more = null,
        public readonly ?string $nextCursor = null,
    ) {}
}
