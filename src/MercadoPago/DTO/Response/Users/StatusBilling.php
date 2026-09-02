<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Status Billing resource. Represents the billing permission status for a user account,
 * indicating whether billing is allowed and any associated status codes.
 */
final class StatusBilling implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Indicates whether billing is allowed (true/false). */
        public readonly ?bool $allow = null,

        /** Billing status codes. */
        public readonly ?array $codes = null,
    ) {}
}
