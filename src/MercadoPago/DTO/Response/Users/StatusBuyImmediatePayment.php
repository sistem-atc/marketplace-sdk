<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Status Immediate Payment resource. Represents the immediate payment requirement for a
 * user's buy/sell actions, indicating whether immediate payment is required and the reasons for
 * it.
 */
final class StatusBuyImmediatePayment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Reasons for immediate payment. */
        public readonly ?array $reasons = null,

        /** Indicates whether immediate payment is required for buying (true/false). */
        public readonly ?bool $required = null,
    ) {}
}
