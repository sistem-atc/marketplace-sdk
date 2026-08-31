<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Uma remessa do plano de inbound, ja' alocada a um armazem.
 *
 * Cada elemento desta lista e' um DESTINO FISICO distinto: no ERP, uma nota de
 * remessa por shipment, com o endereco daquele armazem.
 *
 * @property list<FbtGoodsItem>|null $goodsItems
 * @property list<FbtPlannedCarton>|null $cartons
 * @property list<FbtShipmentOption>|null $shipmentOptions
 */
final class FbtAllocationShipment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $warehouseId = null,
        #[ArrayOf(FbtGoodsItem::class)]
        public readonly ?array $goodsItems = null,
        #[ArrayOf(FbtPlannedCarton::class)]
        public readonly ?array $cartons = null,
        #[ArrayOf(FbtShipmentOption::class)]
        public readonly ?array $shipmentOptions = null,
    ) {}
}
