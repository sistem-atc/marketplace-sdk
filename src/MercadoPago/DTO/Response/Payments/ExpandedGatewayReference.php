<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents gateway reference identifiers from the card network.
 */
final class ExpandedGatewayReference implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique transaction identifier assigned by the card network (e.g. Visa, Mastercard). */
        public readonly ?string $networkTransactionId = null,

        /** Card-network identifiers returned by the gateway. */
        public readonly ?NetworkData $networkData = null,
    ) {}
}
