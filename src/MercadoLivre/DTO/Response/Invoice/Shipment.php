<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Envio da nota (`shipment`) — logistica, transportadora, volumes e locais.
 *
 * @property list<Volume> $volumes
 * @property list<ShippingLocation> $shippingLocations
 */
final class Shipment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<Volume>  $volumes
     * @param  list<ShippingLocation>  $shippingLocations
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $siteId = null,
        public readonly ?string $mode = null,
        public readonly ?string $logisticType = null,
        public readonly ?float $buyerCost = null,
        public readonly ?string $paidBy = null,
        public readonly ?Carrier $carrier = null,
        #[ArrayOf(Volume::class)]
        public readonly array $volumes = [],
        public readonly ?string $fiscalModelId = null,
        #[ArrayOf(ShippingLocation::class)]
        public readonly array $shippingLocations = [],
        public readonly ?string $destination = null,
    ) {}
}
