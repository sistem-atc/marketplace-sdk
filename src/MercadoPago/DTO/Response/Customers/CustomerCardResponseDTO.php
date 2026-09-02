<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a payment card saved to a customer's profile in MercadoPago. Contains tokenized card
 * details (BIN, last four digits, expiration), the associated payment method, issuer, and
 * cardholder information. Card numbers are never stored in full; only partial digits are
 * available for display purposes.
 */
final class CustomerCardResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique card identifier assigned by MercadoPago. */
        public readonly ?string $id = null,

        /** ID of the customer who owns this card. */
        public readonly ?string $customerId = null,

        /** Card expiration month (1-12). */
        public readonly ?int $expirationMonth = null,

        /** Card expiration year (four-digit). */
        public readonly ?int $expirationYear = null,

        /** First six digits (BIN) of the card number, used to identify the issuer and card type. */
        public readonly ?string $firstSixDigits = null,

        /** Last four digits of the card number, used for display/identification. */
        public readonly ?string $lastFourDigits = null,

        /** Payment method associated with this card (e.g., Visa, Mastercard). */
        public readonly ?PaymentMethod $paymentMethod = null,

        /** Security code (CVV/CVC) metadata, including length and card location. */
        public readonly ?SecurityCode $securityCode = null,

        /** Financial institution that issued this card. */
        public readonly ?Issuer $issuer = null,

        /** Cardholder details (name and identification) as printed on the card. */
        public readonly ?Cardholder $cardholder = null,

        /** Timestamp when this card record was created (ISO 8601). */
        public readonly ?string $dateCreated = null,

        /** Timestamp of the last update to this card record (ISO 8601). */
        public readonly ?string $dateLastUpdated = null,

        /** Internal MercadoPago user ID associated with this card. */
        public readonly ?string $userId = null,

        /** Whether this record belongs to a production (true) or test (false) environment. */
        public readonly ?bool $liveMode = null,

        /** Unique identifier for the tokenized card number. */
        public readonly ?string $cardNumberId = null,
    ) {}
}
