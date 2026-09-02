<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Merchant Order resource. Represents a merchant order in the MercadoPago platform, which groups
 * one or more payments associated with a checkout preference. A merchant order tracks the
 * lifecycle of a purchase including its items, payments, shipments, and overall fulfillment
 * status.
 */
final class MerchantOrderResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Order ID. */
        public readonly ?int $id = null,

        /** Payment preference identifier associated to the merchant order. */
        public readonly ?string $preferenceId = null,

        /** Application ID. */
        public readonly ?string $applicationId = null,

        /** Show the current merchant order state. */
        public readonly ?string $status = null,

        /** Country identifier that merchant order belongs to. */
        public readonly ?string $siteId = null,

        /** Payer information. */
        public readonly ?Payer $payer = null,

        /** Seller information. */
        public readonly ?Collector $collector = null,

        /** Sponsor ID. */
        public readonly ?string $sponsorId = null,

        /** Amount paid in this order. */
        public readonly ?float $paidAmount = null,

        /** Amount refunded in this Order. */
        public readonly ?float $refundedAmount = null,

        /** Shipping fee. */
        public readonly ?float $shippingCost = null,

        /** Date of creation. */
        public readonly ?string $dateCreated = null,

        /** Last modified date. */
        public readonly ?string $lastUpdated = null,

        /** If the Order is expired (true) or not (false). */
        public readonly ?bool $cancelled = null,

        /** Payments information. @var list<Payment>|null */
        #[ArrayOf(Payment::class)]
        public readonly ?array $payments = null,

        /** Items information. @var list<Item>|null */
        #[ArrayOf(Item::class)]
        public readonly ?array $items = null,

        /** Shipments information. @var list<Shipment>|null */
        #[ArrayOf(Shipment::class)]
        public readonly ?array $shipments = null,

        /** URL where you'd like to receive a payment notification. */
        public readonly ?string $notificationUrl = null,

        /** Additional information. */
        public readonly ?string $additionalInfo = null,

        /** Reference you can synchronize with your payment system. */
        public readonly ?string $externalReference = null,

        /** Origin of the payment. */
        public readonly ?string $marketplace = null,

        /** Total amount of the order. */
        public readonly ?float $totalAmount = null,

        /** Current merchant order status given the payments status. */
        public readonly ?string $orderStatus = null,

        /** If is test. */
        public readonly ?bool $isTest = null,

        /** Payouts. */
        public readonly ?array $payouts = null,
    ) {}
}
