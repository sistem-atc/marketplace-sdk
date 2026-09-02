<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference Excluded Payment Method resource. Identifies a specific payment method to exclude
 * from the checkout preference. Used within PaymentMethods to restrict which payment methods are
 * available to the buyer.
 */
final class PaymentMethod implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Payment method ID. */
        public readonly ?string $id = null,
    ) {}
}
