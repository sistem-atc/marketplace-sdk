<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the gateway section of expanded payment data.
 */
final class ExpandedGateway implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Reference identifiers returned by the gateway. */
        public readonly ?ExpandedGatewayReference $reference = null,
    ) {}
}
