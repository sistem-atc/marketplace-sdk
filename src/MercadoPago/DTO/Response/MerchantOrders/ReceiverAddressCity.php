<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Merchant Order Receiver Address City resource. Represents the city component of a shipment's
 * receiver address within a merchant order.
 */
final class ReceiverAddressCity implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** City ID. */
        public readonly ?string $id = null,

        /** City name. */
        public readonly ?string $name = null,
    ) {}
}
