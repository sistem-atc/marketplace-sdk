<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\DifferentialPricing;

/**
 * Represents the online checkout configuration for a MercadoPago order. Controls the buyer's
 * redirect flow after completing checkout, including success/failure/pending URLs, tracking
 * pixels, and additional checkout behavior such as differential pricing and 3DS security.
 */
final class OnlineConfig implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** ISO 8601 datetime from which the order is available for payment. */
        public readonly ?string $availableFrom = null,

        /** Restricts who can pay. "account_only" limits to logged-in MercadoPago users; omit to accept all users. */
        public readonly ?string $allowedUserType = null,

        /** URL where MercadoPago sends asynchronous payment notifications (IPN/webhook). */
        public readonly ?string $callbackUrl = null,

        /** URL to redirect the buyer after a successful payment. */
        public readonly ?string $successUrl = null,

        /** URL to redirect the buyer when the payment is pending approval. */
        public readonly ?string $pendingUrl = null,

        /** URL to redirect the buyer after a failed payment. */
        public readonly ?string $failureUrl = null,

        /** Legacy URL field for automatic redirection. Prefer auto_return plus success/failure/pending URLs for online orders. */
        public readonly ?string $autoReturnUrl = null,

        /** Automatic redirect behavior. "approved" redirects to success_url on approval; "all" redirects on any outcome. */
        public readonly ?string $autoReturn = null,

        /** Tracking pixels fired at checkout completion. Supports "google_ad" and "facebook_ad" types. Each element maps to Track. @var list<Track>|null */
        #[ArrayOf(Track::class)]
        public readonly ?array $tracks = null,

        /** Payment retry configuration for this order. */
        public readonly ?Retries $retries = null,

        /** Differential pricing configuration for offering different prices per payment method. @var list<DifferentialPricing>|null */
        #[ArrayOf(DifferentialPricing::class)]
        public readonly ?array $differentialPricing = null,

        /** 3D Secure and other transaction security settings. */
        public readonly ?TransactionSecurity $transactionSecurity = null,
    ) {}
}
