<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Phone resource. Extends the common Phone with a verification flag indicating whether the
 * user's phone number has been confirmed.
 */
final class Phone implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Indicates whether the user's phone number is verified (true/false). */
        public readonly ?bool $verified = null,

        /** Country or regional area code (e.g. "11" for Sao Paulo). */
        public readonly ?string $areaCode = null,

        /** Phone number without area code. */
        public readonly ?string $number = null,

        /** Phone extension number, if applicable. */
        public readonly ?string $extension = null,
    ) {}
}
