<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Bill Data resource. Contains the user's billing preferences, specifically whether the user
 * accepts credit notes for transactions.
 */
final class BillData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Indicates whether the user accepts credit notes (true/false). */
        public readonly ?bool $acceptCreditNote = null,
    ) {}
}
