<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Merchant Order Receiver Address State resource. Represents the state/province component of a
 * shipment's receiver address within a merchant order.
 */
final class ReceiverAddressState implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** State ID. */
        public readonly ?string $id = null,

        /** State name. */
        public readonly ?string $name = null,
    ) {}
}
