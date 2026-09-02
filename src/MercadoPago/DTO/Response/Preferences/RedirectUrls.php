<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference Redirect URLs resource. Defines the redirect URLs used after the buyer completes
 * checkout. Inherits success, pending, and failure URL fields from Urls. Unlike BackUrls, these
 * are used for server-side redirects in specific integration flows.
 */
final class RedirectUrls implements DTOInterface
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
