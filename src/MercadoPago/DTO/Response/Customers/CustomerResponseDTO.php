<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Address;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Identification;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Phone;

/**
 * Represents a saved customer in the MercadoPago platform. Stores payer information (email,
 * identification, address) and saved cards for streamlined checkout experiences. Customers can
 * have multiple cards and addresses associated with their profile.
 */
final class CustomerResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique customer identifier assigned by MercadoPago. */
        public readonly ?string $id = null,

        /** Customer's email address, used as the primary lookup key. */
        public readonly ?string $email = null,

        /** Customer's first name. */
        public readonly ?string $firstName = null,

        /** Customer's last name. */
        public readonly ?string $lastName = null,

        /** Date when the customer registered on the merchant's platform (ISO 8601). */
        public readonly ?string $dateRegistered = null,

        /** Free-text description or notes about the customer. */
        public readonly ?string $description = null,

        /** Timestamp when this customer record was created in MercadoPago (ISO 8601). */
        public readonly ?string $dateCreated = null,

        /** Timestamp of the last update to this customer record (ISO 8601). */
        public readonly ?string $dateLastUpdated = null,

        /** ID of the customer's default card used for payments. */
        public readonly ?string $defaultCard = null,

        /** ID of the customer's default shipping/billing address. */
        public readonly ?string $defaultAddress = null,

        /** Whether this record belongs to a production (true) or test (false) environment. */
        public readonly ?bool $liveMode = null,

        /** Internal MercadoPago user ID linked to this customer. */
        public readonly ?int $userId = null,

        /** ID of the merchant (seller) who owns this customer record. */
        public readonly ?int $merchantId = null,

        /** ID of the OAuth application that created this customer. */
        public readonly ?int $clientId = null,

        /** Customer status (e.g., "active"). */
        public readonly ?string $status = null,

        /** Saved payment cards associated with this customer. @var list<CustomerCardResponseDTO>|null */
        #[ArrayOf(CustomerCardResponseDTO::class)]
        public readonly ?array $cards = null,

        /** Registered addresses associated with this customer. @var list<CustomerAddress>|null */
        #[ArrayOf(CustomerAddress::class)]
        public readonly ?array $addresses = null,

        /** Customer's phone number details. */
        public readonly ?Phone $phone = null,

        /** Customer's personal identification document (e.g., CPF, DNI). */
        public readonly ?Identification $identification = null,

        /** Customer's primary address. */
        public readonly ?Address $address = null,
    ) {}
}
