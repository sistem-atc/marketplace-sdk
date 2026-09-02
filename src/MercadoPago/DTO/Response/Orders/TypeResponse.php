<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents type-specific response data returned for a MercadoPago order. Contains additional
 * output fields that depend on the order type, such as QR code data for QR-based payment orders.
 */
final class TypeResponse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** QR code payload string for QR-based orders (e.g., for in-store payments). */
        public readonly ?string $qrData = null,
    ) {}
}
