<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Context resource. Contains contextual information about the user's current session, such
 * as their IP address.
 */
final class Context implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The user's IP address. */
        public readonly ?string $ipAddress = null,
    ) {}
}
