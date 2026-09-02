<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference Tax resource. Represents a tax applied to a checkout preference. Each tax entry
 * specifies a tax type (e.g. "IVA", "ISC") and its corresponding monetary value.
 */
final class Tax implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Tax type. */
        public readonly ?string $type = null,

        /** Tax value. */
        public readonly ?float $value = null,
    ) {}
}
