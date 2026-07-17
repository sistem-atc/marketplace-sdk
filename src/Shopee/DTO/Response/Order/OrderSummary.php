<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `/api/v2/order/get_order_list` — só o identificador do pedido
 * (a Shopee não devolve o pedido inteiro na listagem; use `getOrderDetail`
 * com os `order_sn` daqui). `orderStatus` só vem quando pedido no filtro.
 */
final class OrderSummary implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderSn = null,
        public readonly ?string $bookingSn = null,
        public readonly ?string $orderStatus = null,
    ) {}
}
