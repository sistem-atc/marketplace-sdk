<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the payment retry configuration for a Checkout PRO order. Controls whether automatic
 * payment retries are enabled when a payment attempt fails within the checkout flow.
 */
final class Retries implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Whether automatic payment retry is enabled. When false, a failed attempt ends the order. */
        public readonly ?bool $allowed = null,
    ) {}
}
