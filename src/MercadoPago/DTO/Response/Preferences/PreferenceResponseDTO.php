<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\DifferentialPricing;

/**
 * Preference resource. Represents a checkout preference in MercadoPago, which defines the payment
 * experience for buyers. A preference configures items to purchase, accepted payment methods,
 * shipping options, callback URLs, expiration rules, and tracking integration. The init_point URL
 * redirects buyers to the MercadoPago Checkout flow.
 */
final class PreferenceResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Preference ID. */
        public readonly ?string $id = null,

        /** List of items to be paid. @var list<Item>|null */
        #[ArrayOf(Item::class)]
        public readonly ?array $items = null,

        /** Payer information. */
        public readonly ?Payer $payer = null,

        /** Client ID. */
        public readonly ?string $clientId = null,

        /** Set up payment methods. */
        public readonly ?PaymentMethods $paymentMethods = null,

        /** URLs to return to the sellers website. */
        public readonly ?BackUrls $backUrls = null,

        /** URLs to redirect to the sellers website. */
        public readonly ?RedirectUrls $redirectUrls = null,

        /** Shipments information. */
        public readonly ?Shipments $shipments = null,

        /** URL where you'd like to receive a payment notification. */
        public readonly ?string $notificationUrl = null,

        /** How the payment will be specified in the card bill. */
        public readonly ?string $statementDescriptor = null,

        /** Reference you can synchronize with your payment system. */
        public readonly ?string $externalReference = null,

        /** True if a preference expires, false if not. */
        public readonly ?bool $expires = null,

        /** Expiration date of cash payment. */
        public readonly ?string $dateOfExpiration = null,

        /** Date when the preference will be active. */
        public readonly ?string $expirationDateFrom = null,

        /** Date when the preference will be expired. */
        public readonly ?string $expirationDateTo = null,

        /** Collector ID. */
        public readonly ?int $collectorId = null,

        /** Origin of the payment. Default value: NONE. */
        public readonly ?string $marketplace = null,

        /** Marketplace's fee charged by application owner. */
        public readonly ?float $marketplaceFee = null,

        /** Additional info. */
        public readonly ?string $additionalInfo = null,

        /** If specified, your buyers will be redirected back to your site immediately after completing the purchase. */
        public readonly ?string $autoReturn = null,

        /** Operation type. */
        public readonly ?string $operationType = null,

        /** Differential pricing configuration for this preference. @var list<DifferentialPricing>|null */
        #[ArrayOf(DifferentialPricing::class)]
        public readonly ?array $differentialPricing = null,

        /** Configures which processing modes to use. */
        public readonly ?array $processingModes = null,

        /** When set to true, the payment can only be approved or rejected. Otherwise in_process status is added. */
        public readonly ?bool $binaryMode = null,

        /** Taxes for preferences. @var list<Tax>|null */
        #[ArrayOf(Tax::class)]
        public readonly ?array $taxes = null,

        /** Tracks to be executed during the users interaction in the Checkout flow. @var list<Tracks>|null */
        #[ArrayOf(Tracks::class)]
        public readonly ?array $tracks = null,

        /** Data that can be attached to the preference to record additional attributes of the merchant. */
        public readonly ?array $metadata = null,

        /** Checkout URL from preference. */
        public readonly ?string $initPoint = null,

        /** Sandbox checkout URL from preference. */
        public readonly ?string $sandboxInitPoint = null,

        /** Date of creation. */
        public readonly ?string $dateCreated = null,

        /** Coupon code. */
        public readonly ?string $couponCode = null,

        /** Coupon labels. */
        public readonly ?array $couponLabels = null,

        /** internal metadata. */
        public readonly ?array $internalMetadata = null,

        /** Site ID. */
        public readonly ?string $siteId = null,

        /** Product ID. */
        public readonly ?string $productId = null,

        /** Live mode. */
        public readonly ?bool $liveMode = null,

        /** Last modified. */
        public readonly ?string $lastUpdated = null,

        /** Purpose. */
        public readonly ?string $purpose = null,

        /** Total amount. */
        public readonly ?int $totalAmount = null,

        /** Headers. */
        public readonly mixed $headers = null,

        /** Created source. */
        public readonly mixed $createdSource = null,

        /** Created by app. */
        public readonly mixed $createdByApp = null,

        /** (campo presente na resposta real, ausente no SDK oficial) */
        public readonly ?array $collector = null,
    ) {}
}
