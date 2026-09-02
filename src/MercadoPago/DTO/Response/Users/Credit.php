<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Credit resource. Represents the MercadoPago credit/lending information for a user account,
 * including consumed credit amount, credit level, and rank classification.
 */
final class Credit implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The amount of consumed credit. */
        public readonly ?int $consumed = null,

        /** The credit level ID. */
        public readonly ?string $creditLevelId = null,

        /** The user's credit rank. */
        public readonly ?string $rank = null,
    ) {}
}
