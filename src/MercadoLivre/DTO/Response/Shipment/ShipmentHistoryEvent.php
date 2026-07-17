<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Shipment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Evento do histórico do envio (GET /shipments/{id}/history com x-format-new).
 * Cada entry é uma transição: `{date, status, substatus}`.
 */
final class ShipmentHistoryEvent implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $status = null,
        public readonly ?string $substatus = null,
        public readonly ?string $date = null,
        public readonly ?string $dateCreated = null,
    ) {}
}
