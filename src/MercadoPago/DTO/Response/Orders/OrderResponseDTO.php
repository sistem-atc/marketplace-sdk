<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a MercadoPago Order resource. An order is the top-level entity that groups items,
 * payer information, transactions (payments, refunds, chargebacks), shipping details, and
 * configuration for a purchase flow in the MercadoPago Orders API.
 */
final class OrderResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier of the order assigned by MercadoPago. */
        public readonly ?string $id = null,

        /** Order type (e.g., "online" for e-commerce transactions). */
        public readonly ?string $type = null,

        /** Seller-defined reference to correlate the order with an external system. */
        public readonly ?string $externalReference = null,

        /** ISO 3166-1 alpha-2 country code where the order is processed. */
        public readonly ?string $countryCode = null,

        /** Current high-level status of the order (e.g., "opened", "closed", "expired"). */
        public readonly ?string $status = null,

        /** Granular detail complementing the order status. */
        public readonly ?string $statusDetail = null,

        /** Determines how funds are captured (e.g., "automatic" or "manual"). */
        public readonly ?string $captureMode = null,

        /** MercadoPago user ID of the seller who owns the order. */
        public readonly ?string $userId = null,

        /** URL to redirect the buyer to the Checkout PRO payment flow. Generated automatically on order creation. */
        public readonly ?string $checkoutUrl = null,

        /** Temporary token identifying the client session for the checkout. */
        public readonly ?string $clientToken = null,

        /** Total amount of the order before any discounts or fees. */
        public readonly ?string $totalAmount = null,

        /** Total amount effectively paid by the buyer. */
        public readonly ?string $totalPaidAmount = null,

        /** Processing mode for payments (e.g., "aggregator", "gateway"). */
        public readonly ?string $processingMode = null,

        /** Short description of the order shown to the buyer. */
        public readonly ?string $description = null,

        /** Marketplace identifier when the order is placed through a marketplace. */
        public readonly ?string $marketplace = null,

        /** Fee charged by the marketplace on this order. */
        public readonly ?string $marketplaceFee = null,

        /** ISO 8601 timestamp when the order was created. */
        public readonly ?string $createdDate = null,

        /** ISO 8601 timestamp of the last update to the order. */
        public readonly ?string $lastUpdatedDate = null,

        /** ISO 8601 timestamp indicating when the checkout becomes available. */
        public readonly ?string $checkoutAvailableAt = null,

        /** ISO 8601 duration or timestamp after which the order expires. */
        public readonly ?string $expirationTime = null,

        /** Integration metadata linking the order to a platform, integrator, or sponsor. */
        public readonly ?IntegrationData $integrationData = null,

        /** Buyer information associated with this order. */
        public readonly ?Payer $payer = null,

        /** Transaction container holding payments, refunds, and chargebacks. */
        public readonly ?OrderTransactionsResponseDTO $transactions = null,

        /** Line items included in the order. Each element maps to Items. @var list<Items>|null */
        #[ArrayOf(Items::class)]
        public readonly ?array $items = null,

        /** Order-level configuration for payment methods and online checkout. */
        public readonly ?Config $config = null,

        /** Arbitrary key-value pairs with additional context about the order. */
        public readonly ?array $additionalInfo = null,

        /** Shipping details and delivery address for the order. */
        public readonly ?Shipment $shipment = null,

        /** ISO 4217 currency code for the order amounts (e.g., "BRL", "ARS"). */
        public readonly ?string $currency = null,

        /** Discount rules applied to the order by payment method. @var list<Discounts>|null */
        #[ArrayOf(Discounts::class)]
        public readonly ?array $discounts = null,

        /** Tax entries applied to the order. Each element maps to Taxes. @var list<Taxes>|null */
        #[ArrayOf(Taxes::class)]
        public readonly ?array $taxes = null,

        /** Type-specific response data (e.g., QR code for QR-based orders). */
        public readonly ?TypeResponse $typeResponse = null,

        /** (campo presente na resposta real, ausente no SDK oficial) */
        public readonly ?string $siteId = null,
    ) {}
}
