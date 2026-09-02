<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Paging;

/**
 * Point Devices list resource. Represents the paginated list of Point smart terminal devices
 * associated with the seller's account. Each device entry includes its ID, POS/store association,
 * and operating mode.
 */
final class PointDevicesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Search paging. */
        public readonly ?Paging $paging = null,

        /** Devices. @var list<Device>|null */
        #[ArrayOf(Device::class)]
        public readonly ?array $devices = null,
    ) {}
}
