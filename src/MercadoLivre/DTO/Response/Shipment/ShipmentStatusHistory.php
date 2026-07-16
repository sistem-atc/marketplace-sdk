<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Shipment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `status_history` — as datas-marco do envio (handling→shipped→delivered/…).
 */
final class ShipmentStatusHistory implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $dateHandling = null,
        public readonly ?string $dateReadyToShip = null,
        public readonly ?string $dateShipped = null,
        public readonly ?string $dateFirstVisit = null,
        public readonly ?string $dateDelivered = null,
        public readonly ?string $dateNotDelivered = null,
        public readonly ?string $dateReturned = null,
        public readonly ?string $dateCancelled = null,
    ) {}
}
