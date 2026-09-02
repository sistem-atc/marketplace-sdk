<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference Excluded Payment Type resource. Identifies a payment type to exclude from the
 * checkout preference (e.g. "ticket", "credit_card"). Used within PaymentMethods to restrict
 * which payment type categories are available to the buyer.
 */
final class PaymentType implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Payment type ID. */
        public readonly ?string $id = null,
    ) {}
}
