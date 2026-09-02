<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Merchant Order Receiver Address Country resource. Represents the country component of a
 * shipment's receiver address within a merchant order.
 */
final class ReceiverAddressCountry implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Country ID. */
        public readonly ?string $id = null,

        /** Country name. */
        public readonly ?string $name = null,
    ) {}
}
