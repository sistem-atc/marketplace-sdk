<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Address;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Identification;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Phone;

/**
 * Preference Payer resource. Represents the buyer information within a checkout preference.
 * Pre-filling payer details (name, email, phone, identification, address) streamlines the
 * checkout experience by reducing data entry for the buyer. Fields are mapped to common DTOs: -
 * identification -> Identification - phone -> Phone - address -> Address
 */
final class Payer implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Payer's name. */
        public readonly ?string $name = null,

        /** Payer's surname. */
        public readonly ?string $surname = null,

        /** Payer's email. */
        public readonly ?string $email = null,

        /** Payer's phone. */
        public readonly ?Phone $phone = null,

        /** Payer's identification. */
        public readonly ?Identification $identification = null,

        /** Payer's address. */
        public readonly ?Address $address = null,

        /** Date of creation of the payer user. */
        public readonly ?string $dateCreated = null,

        /** Date of the last purchase. */
        public readonly ?string $lastPurchase = null,

        /** Authentication type used by the payer. */
        public readonly ?string $authenticationType = null,

        /** Whether the payer is a prime user. */
        public readonly ?bool $isPrimeUser = null,

        /** Whether this is the payer's first online purchase. */
        public readonly ?bool $isFirstPurchaseOnline = null,

        /** ISO 8601 date when the payer registered. */
        public readonly ?string $registrationDate = null,
    ) {}
}
