<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Address;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Identification;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Phone;

/**
 * Represents extended payer details provided as part of payment additional info. Supplements the
 * main Payer data with contact details and registration history that help MercadoPago improve
 * fraud analysis.
 */
final class AdditionalInfoPayer implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Payer's given/first name. */
        public readonly ?string $firstName = null,

        /** Payer's family/last name. */
        public readonly ?string $lastName = null,

        /** Payer's contact phone number. */
        public readonly ?Phone $phone = null,

        /** Payer's billing or residential address. */
        public readonly ?Address $address = null,

        /** ISO 8601 date when the payer registered on the integrator's platform. */
        public readonly ?string $registrationDate = null,

        /** Authentication type used by the payer. */
        public readonly ?string $authenticationType = null,

        /** Whether the payer is a prime user. */
        public readonly ?bool $isPrimeUser = null,

        /** Whether this is the payer's first online purchase. */
        public readonly ?bool $isFirstPurchaseOnline = null,

        /** ISO 8601 date of the payer's last purchase. */
        public readonly ?string $lastPurchase = null,

        /** Payer's identification document. */
        public readonly ?Identification $identification = null,
    ) {}
}
