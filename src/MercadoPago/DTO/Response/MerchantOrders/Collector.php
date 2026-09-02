<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Merchant Order Collector (seller) resource. Represents the seller who receives the payment for
 * a merchant order. Contains basic identification and contact details of the collecting party.
 */
final class Collector implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Collector ID. */
        public readonly ?int $id = null,

        /** Collector nickname. */
        public readonly ?string $nickname = null,

        /** Email. */
        public readonly ?string $email = null,
    ) {}
}
