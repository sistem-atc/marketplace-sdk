<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Alternative Phone resource. Represents a secondary/alternative phone number associated
 * with a user account, including area code, phone number, and optional extension.
 */
final class AlternativePhone implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The area code of the user's phone number. */
        public readonly ?string $areaCode = null,

        /** The extension of the user's phone number (if available). */
        public readonly ?string $extension = null,

        /** The user's phone number. */
        public readonly ?string $number = null,
    ) {}
}
