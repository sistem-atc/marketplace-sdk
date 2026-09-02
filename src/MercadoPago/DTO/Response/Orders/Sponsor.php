<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a sponsor account within the order's integration data. A sponsor is a MercadoPago
 * account that receives a portion of the fees when orders are created through a marketplace or
 * platform integration.
 */
final class Sponsor implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** MercadoPago account ID of the sponsor. */
        public readonly ?string $id = null,
    ) {}
}
