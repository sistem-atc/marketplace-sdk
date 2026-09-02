<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference Back URLs resource. Defines the callback URLs where the buyer is redirected after
 * completing, cancelling, or encountering a pending state during the MercadoPago Checkout flow.
 * Inherits success, pending, and failure URL fields from Urls.
 */
final class BackUrls implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** URL to when the payment succeed. */
        public readonly ?string $success = null,

        /** URL to when the payment is pending. */
        public readonly ?string $pending = null,

        /** URL to when the payment fail. */
        public readonly ?string $failure = null,
    ) {}
}
