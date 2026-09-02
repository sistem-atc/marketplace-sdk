<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the card details associated with a card-based payment in the MercadoPago API.
 * Contains masked card data (BIN, last four digits), expiration, and cardholder info. Nested
 * within the Payment response for credit/debit card payments.
 */
final class Card implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier of the card (if stored/saved). */
        public readonly ?string $id = null,

        /** Last four digits of the card number (for display/verification). */
        public readonly ?string $lastFourDigits = null,

        /** First six digits (BIN) of the card number, identifying the issuer and card type. */
        public readonly ?string $firstSixDigits = null,

        /** Four-digit expiration year of the card. */
        public readonly int|string|null $expirationYear = null,

        /** Two-digit expiration month of the card (1-12). */
        public readonly int|string|null $expirationMonth = null,

        /** ISO 8601 timestamp when the card record was created. */
        public readonly ?string $dateCreated = null,

        /** ISO 8601 timestamp of the last update to the card record. */
        public readonly ?string $dateLastUpdated = null,

        /** ISO 3166-1 country code where the card was issued. */
        public readonly ?string $country = null,

        /** Name and identification of the cardholder. */
        public readonly ?Cardholder $cardholder = null,

        /** Bank Identification Number (first 6-8 digits of the card). */
        public readonly ?string $bin = null,

        /** Internal tags associated with the card. */
        public readonly ?array $tags = null,
    ) {}
}
