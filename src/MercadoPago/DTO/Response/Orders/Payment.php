<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents an individual payment within a MercadoPago order transaction. An order may contain
 * one or more payments (e.g., split payments). Each payment tracks its own amount, status,
 * payment method, retry attempts, and refund totals.
 */
final class Payment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier of this payment assigned by MercadoPago. */
        public readonly ?string $id = null,

        /** Seller-defined reference to correlate this payment with an external system. */
        public readonly ?string $referenceId = null,

        /** Current payment status (e.g., "approved", "pending", "rejected"). */
        public readonly ?string $status = null,

        /** Granular detail complementing the payment status (e.g., "accredited", "pending_waiting_transfer"). */
        public readonly ?string $statusDetail = null,

        /** Requested payment amount in the order's currency. */
        public readonly ?string $amount = null,

        /** Amount effectively collected from the buyer. */
        public readonly ?string $paidAmount = null,

        /** ISO 8601 timestamp after which the payment can no longer be completed. */
        public readonly ?string $dateOfExpiration = null,

        /** Duration or timestamp defining how long the payment remains valid. */
        public readonly ?string $expirationTime = null,

        /** Current retry attempt number for this payment. */
        public readonly ?int $attemptNumber = null,

        /** History of payment attempts. Each element maps to Attempt. @var list<Attempt>|null */
        #[ArrayOf(Attempt::class)]
        public readonly ?array $attempts = null,

        /** Payment method used for this payment (card, Pix, boleto, etc.). */
        public readonly ?PaymentMethod $paymentMethod = null,

        /** Automatic/recurring payment configuration. */
        public readonly ?AutomaticPayments $automaticPayments = null,

        /** Stored credential data for card-on-file or recurring transactions. @var list<StoredCredential>|null */
        #[ArrayOf(StoredCredential::class)]
        public readonly ?array $storedCredential = null,

        /** Subscription billing data when this payment is part of a recurring plan. */
        public readonly ?SubscriptionData $subscriptionData = null,

        /** Total amount that has been refunded for this payment. */
        public readonly ?string $refundedAmount = null,

        /** Payment provider or acquirer processing this payment. */
        public readonly ?string $provider = null,

        /** Discounts applied to this specific payment. Each element maps to PaymentDiscount. @var list<PaymentDiscount>|null */
        #[ArrayOf(PaymentDiscount::class)]
        public readonly ?array $discounts = null,
    ) {}
}
