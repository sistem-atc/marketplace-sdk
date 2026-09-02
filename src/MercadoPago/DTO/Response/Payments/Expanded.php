<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents expanded response data included in a payment when gateway mode is used. Contains
 * additional gateway-level information such as network transaction references. Nested within
 * Payment when expanded fields are requested.
 */
final class Expanded implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Gateway-specific response data including network references. */
        public readonly ?ExpandedGateway $gateway = null,
    ) {}
}
