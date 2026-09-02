<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a payment processed through the MercadoPago Payments API. Contains all payment
 * details including amount, status, payment method, payer information, and transaction metadata.
 * Returned by PaymentClient operations (create, get, update, cancel, capture).
 */
final class PaymentResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique payment identifier assigned by MercadoPago. */
        public readonly ?int $id = null,

        /** Acquirer reconciliation data for matching with bank/acquirer records. */
        public readonly ?array $acquirerReconciliation = null,

        /** MercadoPago site/country identifier (e.g. "MLA", "MLB", "MLM"). */
        public readonly ?string $siteId = null,

        /** Identifier of the sponsor in a marketplace or platform integration. */
        public readonly ?int $sponsorId = null,

        /** Type of operation (e.g. "regular_payment", "money_transfer"). */
        public readonly ?string $operationType = null,

        /** Associated order reference. */
        public readonly ?Order $order = null,

        /** Brand identifier for white-label integrations. */
        public readonly ?string $brandId = null,

        /** SDK or API build version used to create the payment. */
        public readonly ?string $buildVersion = null,

        /** When true, the payment can only result in "approved" or "rejected" (no "in_process"). */
        public readonly ?bool $binaryMode = null,

        /** External reference ID set by the integrator for reconciliation. */
        public readonly ?string $externalReference = null,

        /** Financing group identifier for grouped installment plans. */
        public readonly ?string $financingGroup = null,

        /** Current payment status (e.g. "approved", "rejected", "pending", "in_process"). */
        public readonly ?string $status = null,

        /** Detailed reason for the current status (e.g. "accredited", "cc_rejected_other_reason"). */
        public readonly ?string $statusDetail = null,

        /** Identifier of the physical store where the payment originated. */
        public readonly ?string $storeId = null,

        /** Total tax amount included in the payment. */
        public readonly ?int $taxesAmount = null,

        /** ISO 8601 timestamp when the payment was created. */
        public readonly ?string $dateCreated = null,

        /** Whether the payment was created in production (true) or sandbox (false). */
        public readonly ?bool $liveMode = null,

        /** ISO 8601 timestamp of the last update to the payment. */
        public readonly ?string $dateLastUpdated = null,

        /** ISO 8601 timestamp when the payment expires (for pending payments). */
        public readonly ?string $dateOfExpiration = null,

        /** Schema that defines how deductions are applied. */
        public readonly ?string $deductionSchema = null,

        /** ISO 8601 timestamp when the payment was approved. */
        public readonly ?string $dateApproved = null,

        /** ISO 8601 date when the funds will be released to the seller. */
        public readonly ?string $moneyReleaseDate = null,

        /** Schema that defines the money release schedule. */
        public readonly ?string $moneyReleaseSchema = null,

        /** Current status of the money release process. */
        public readonly ?string $moneyReleaseStatus = null,

        /** ISO 4217 currency code (e.g. "ARS", "BRL", "MXN"). */
        public readonly ?string $currencyId = null,

        /** Total amount charged to the payer. */
        public readonly ?float $transactionAmount = null,

        /** Cumulative amount that has been refunded for this payment. */
        public readonly ?float $transactionAmountRefunded = null,

        /** Payer information (buyer). */
        public readonly ?Payer $payer = null,

        /** Data forwarded to acquirers (sub-merchant, network transaction). */
        public readonly ?ForwardData $forwardData = null,

        /** MercadoPago user ID of the payment collector (seller). */
        public readonly ?int $collectorId = null,

        /** Counter-currency identifier for cross-border payments. */
        public readonly ?string $counterCurrency = null,

        /** Identifier of the payment method used (e.g. "visa", "pix", "bolbradesco"). */
        public readonly ?string $paymentMethodId = null,

        /** Detailed payment method information. */
        public readonly ?PaymentMethod $paymentMethod = null,

        /** Payment method type (e.g. "credit_card", "debit_card", "ticket", "bank_transfer"). */
        public readonly ?string $paymentTypeId = null,

        /** Identifier of the Point of Sale device, if applicable. */
        public readonly ?string $posId = null,

        /** Financial details of the transaction. */
        public readonly ?TransactionDetails $transactionDetails = null,

        /** List of fees applied to the payment. @var list<FeeDetails>|null */
        #[ArrayOf(FeeDetails::class)]
        public readonly ?array $feeDetails = null,

        /** Identifier of the differential pricing plan applied. */
        public readonly ?string $differentialPricingId = null,

        /** Fee charged by the marketplace to the seller on this payment. */
        public readonly ?float $applicationFee = null,

        /** Authorization code returned by the card issuer. */
        public readonly ?string $authorizationCode = null,

        /** Whether the payment amount has been captured (relevant for two-step payments). */
        public readonly ?bool $captured = null,

        /** Card details used in the payment. */
        public readonly ?Card $card = null,

        /** Identifier for call-for-authorize flows when the issuer requires phone confirmation. */
        public readonly ?string $callForAuthorizeId = null,

        /** Text that appears on the payer's card statement. */
        public readonly ?string $statementDescriptor = null,

        /** Shipping cost amount included in the payment. */
        public readonly ?float $shippingAmount = null,

        /** Additional information about items, payer, and shipment. */
        public readonly ?AdditionalInfo $additionalInfo = null,

        /** Discount coupon amount applied to the payment. */
        public readonly ?float $couponAmount = null,

        /** Number of installments chosen by the payer. */
        public readonly ?int $installments = null,

        /** Card token generated by MercadoPago.js for secure card data handling. */
        public readonly ?string $token = null,

        /** Short description of the payment purpose shown to the payer. */
        public readonly ?string $description = null,

        /** URL where MercadoPago sends webhook notifications for status changes. */
        public readonly ?string $notificationUrl = null,

        /** Identifier of the card issuer. */
        public readonly ?string $issuerId = null,

        /** Processing mode for the payment (e.g. "aggregator", "gateway"). */
        public readonly ?string $processingMode = null,

        /** Merchant account identifier for gateway mode processing. */
        public readonly ?string $merchantAccountId = null,

        /** Merchant number assigned by the acquirer. */
        public readonly ?string $merchantNumber = null,

        /** Custom key-value metadata attached to the payment. */
        public readonly ?Metadata $metadata = null,

        /** URL to redirect the payer after a payment attempt. */
        public readonly ?string $callbackUrl = null,

        /** Coupon code applied by the payer at checkout. */
        public readonly ?string $couponCode = null,

        /** Information about the money release process. */
        public readonly ?string $releaseInfo = null,

        /** Marketplace owner identifier. */
        public readonly ?string $marketplaceOwner = null,

        /** Integrator identifier for tracking third-party platform integrations. */
        public readonly ?string $integratorId = null,

        /** Corporation identifier for enterprise-level tracking. */
        public readonly ?string $corporationId = null,

        /** Platform identifier for multi-platform tracking. */
        public readonly ?string $platformId = null,

        /** Breakdown of charges applied to the payment. */
        public readonly ?array $chargesDetails = null,

        /** Tax details applied to the payment. */
        public readonly ?array $taxes = null,

        /** Net amount received by the seller after fees. */
        public readonly ?float $netAmount = null,

        /** Information about where the payment interaction occurred (e.g. QR, deep link). */
        public readonly ?PointOfInteraction $pointOfInteraction = null,

        /** Account-level information related to the transaction. */
        public readonly ?array $accountsInfo = null,

        /** Internal tags associated with the payment. */
        public readonly ?array $tags = null,

        /** List of refunds applied to this payment. */
        public readonly ?array $refunds = null,

        /** 3D Secure authentication information. */
        public readonly ?ThreeDSInfo $threeDsInfo = null,

        /** Expanded response data (e.g. gateway references). */
        public readonly ?Expanded $expanded = null,

        /** Identifier of the associated order (legacy field). */
        public readonly ?int $orderId = null,

        /** Total shipping cost for the order. */
        public readonly ?int $shippingCost = null,

        /** (campo presente na resposta real, ausente no SDK oficial) */
        public readonly ?array $internalMetadata = null,
    ) {}
}
