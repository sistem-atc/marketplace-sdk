<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents subscription billing data associated with an order payment. Contains information
 * about the recurring billing cycle, including which invoice in the sequence this payment
 * corresponds to and the billing period.
 */
final class SubscriptionData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Position of this payment within the subscription series. */
        public readonly ?SubscriptionSequence $subscriptionSequence = null,

        /** Unique identifier of the invoice being paid. */
        public readonly ?string $invoiceId = null,

        /** Billing period definition for this subscription cycle. */
        public readonly ?InvoicePeriod $invoicePeriod = null,

        /** ISO 8601 date when this subscription billing was generated. */
        public readonly ?string $billingDate = null,
    ) {}
}
