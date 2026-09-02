<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference Free Shipping Method resource. Identifies a shipping method that should be offered
 * as free shipping within a preference. Used in "me2" (MercadoEnvios) shipping mode to subsidize
 * specific carrier methods.
 */
final class FreeMethod implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Shipping method ID. */
        public readonly ?int $id = null,
    ) {}
}
