<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Shared;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the origin source of an action in the MercadoPago API. Typically used within refund
 * responses to indicate which actor or system initiated the refund (e.g. the collector, the
 * buyer, or MercadoPago itself).
 */
final class Source implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier of the source actor. */
        public readonly ?string $id = null,

        /** Display name of the source actor. */
        public readonly ?string $name = null,

        /** Type of source (e.g. "collector", "buyer", "admin"). */
        public readonly ?string $type = null,
    ) {}
}
