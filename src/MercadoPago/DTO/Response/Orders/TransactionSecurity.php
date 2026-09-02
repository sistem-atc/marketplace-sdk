<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents 3D Secure and other transaction security details for an order payment. Tracks the
 * authentication status, liability shift outcome, and challenge URL for card payments requiring
 * strong customer authentication (SCA/3DS).
 */
final class TransactionSecurity implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier of the 3DS authentication transaction. */
        public readonly ?string $id = null,

        /** Authentication validation status (e.g., "automatic", "manual"). */
        public readonly ?string $validation = null,

        /** Whether liability shifted to the issuer after 3DS authentication (e.g., "yes", "no"). */
        public readonly ?string $liabilityShift = null,

        /** Challenge URL where the buyer must complete 3DS authentication. */
        public readonly ?string $url = null,
    ) {}
}
