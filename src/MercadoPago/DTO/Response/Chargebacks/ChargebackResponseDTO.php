<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Chargebacks;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Chargeback resource. Represents a payment dispute initiated by a cardholder through their
 * issuing bank. Chargebacks are created by MercadoPago when a dispute is opened and track the
 * dispute amount, status, and reason.
 */
final class ChargebackResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The chargeback ID. */
        public readonly ?string $id = null,

        /** The ID of the payment that originated the dispute. */
        public readonly ?int $paymentId = null,

        /** The current status of the chargeback. */
        public readonly ?string $status = null,

        /** The disputed amount. */
        public readonly ?float $amount = null,

        /** The ISO 4217 currency code of the disputed amount. */
        public readonly ?string $currencyId = null,

        /** The reason code provided by the card network. */
        public readonly ?string $reasonId = null,

        /** The textual description of the dispute reason. */
        public readonly ?string $reason = null,

        /** The date and time when the chargeback was created. */
        public readonly ?string $dateCreated = null,

        /** The date and time of the last modification. */
        public readonly ?string $lastModified = null,
    ) {}
}
