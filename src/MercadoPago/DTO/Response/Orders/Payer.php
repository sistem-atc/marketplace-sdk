<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Address;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Identification;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Phone;

/**
 * Represents the buyer (payer) associated with a MercadoPago order. Contains the buyer's personal
 * information used for payment processing, fraud prevention, and receipt generation.
 */
final class Payer implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** MercadoPago customer ID if the payer is a registered customer. */
        public readonly ?string $customerId = null,

        /** Legal entity type of the payer (e.g., "individual", "association"). */
        public readonly ?string $entityType = null,

        /** Payer's email address used for notifications and receipts. */
        public readonly ?string $email = null,

        /** Payer's first name. */
        public readonly ?string $firstName = null,

        /** Payer's last name. */
        public readonly ?string $lastName = null,

        /** Payer's identification document (type and number). */
        public readonly ?Identification $identification = null,

        /** Payer's phone number details. */
        public readonly ?Phone $phone = null,

        /** Payer's address details. */
        public readonly ?Address $address = null,
    ) {}
}
