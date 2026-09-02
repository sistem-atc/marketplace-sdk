<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents stored credential (card-on-file) data for an order payment. Used to indicate whether
 * a payment uses previously stored card credentials and whether the transaction is
 * merchant-initiated or cardholder-initiated, as required by card network regulations.
 */
final class StoredCredential implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Who initiated the payment: "cardholder" or "merchant". */
        public readonly ?string $paymentInitiator = null,

        /** Reason for using stored credentials (e.g., "recurring", "installment", "unscheduled"). */
        public readonly ?string $reason = null,

        /** Whether to store the payment method for future transactions. */
        public readonly ?bool $storePaymentMethod = null,

        /** Whether this is the first payment in a series using these credentials. */
        public readonly ?bool $firstPayment = null,

        /** Reference to the previous transaction in a recurring series. Required from the second charge onwards to link this payment to the original card-network authorization. Type: string (transaction ID). */
        public readonly ?string $previousTransactionReference = null,
    ) {}
}
