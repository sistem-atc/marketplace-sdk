<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\Address;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\Identifications;

/**
 * Local de coleta/entrega do envio (`shipment.shipping_locations[]`).
 */
final class ShippingLocation implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?Identifications $identifications = null,
        public readonly ?Address $address = null,
    ) {}
}
