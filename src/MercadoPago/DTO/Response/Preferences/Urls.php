<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference URLs base resource. Base class for checkout redirect URLs. Defines the three
 * outcome-based URLs (success, pending, failure) that the buyer is redirected to after checkout.
 * Extended by BackUrls and RedirectUrls.
 */
final class Urls implements DTOInterface
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
