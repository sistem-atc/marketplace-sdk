<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Shipment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\NamedRef;

/**
 * `shipping_option` — modalidade/custo/prazos do envio.
 */
final class ShipmentOption implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?float $cost = null,
        public readonly ?float $listCost = null,
        public readonly ?string $currencyId = null,
        public readonly ?string $deliveryType = null,
        public readonly ?int $shippingMethodId = null,
        // priority_class vem como objeto {id} (não escalar).
        public readonly ?NamedRef $priorityClass = null,
        public readonly mixed $buffering = null,
        public readonly mixed $pickupPromise = null,
        public readonly mixed $processingTime = null,
        public readonly mixed $deliveryPromise = null,
        public readonly mixed $estimatedDeliveryTime = null,
        public readonly mixed $estimatedDeliveryFinal = null,
        public readonly mixed $estimatedDeliveryLimit = null,
        public readonly mixed $estimatedScheduleLimit = null,
        public readonly mixed $desiredPromisedDelivery = null,
        public readonly mixed $estimatedDeliveryExtended = null,
    ) {}
}
