<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Identification;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Phone;

/**
 * Represents the payer (buyer) of a payment in the MercadoPago API. Contains the payer's
 * identity, contact information, and identification documents. This is the primary payer object
 * nested within Payment.
 */
final class Payer implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Payer type identifier (e.g. "customer", "registered", "guest"). Mandatory when the payer is a saved Customer. */
        public readonly ?string $type = null,

        /** Unique payer identifier in MercadoPago. */
        public readonly ?string $id = null,

        /** Payer's email address. */
        public readonly ?string $email = null,

        /** Payer's identification document (e.g. CPF, DNI). */
        public readonly ?Identification $identification = null,

        /** Payer's given/first name. */
        public readonly ?string $firstName = null,

        /** Payer's family/last name. */
        public readonly ?string $lastName = null,

        /** Entity type for bank transfer payments (e.g. "individual", "association"). */
        public readonly ?string $entityType = null,

        /** Payer's contact phone number. */
        public readonly ?Phone $phone = null,

        /** Operator identifier when the payment is initiated by an operator on behalf of the payer. */
        public readonly ?string $operatorId = null,
    ) {}
}
