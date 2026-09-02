<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the point of interaction where the payment was initiated in the MercadoPago API.
 * Describes the channel or device through which the payer interacted to make the payment, such as
 * a QR code, deep link, or IVR system. Nested within Payment.
 */
final class PointOfInteraction implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Interaction type (e.g. "OPENPLATFORM", "ATML", "QR"). */
        public readonly ?string $type = null,

        /** Interaction subtype providing additional classification. */
        public readonly ?string $subType = null,

        /** Application that generated the interaction. */
        public readonly ?ApplicationData $applicationData = null,

        /** Transaction data generated at the point of interaction (e.g. QR content, ticket URL). */
        public readonly ?TransactionData $transactionData = null,
    ) {}
}
