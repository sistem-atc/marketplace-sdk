<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pacote/volume do pedido (`package_list[]`) — 1 pedido pode ter N pacotes.
 *
 * @property list<PackageItem>|null $itemList
 */
final class OrderPackage implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $packageNumber = null,
        public readonly ?string $logisticsStatus = null,
        public readonly ?int $logisticsChannelId = null,
        public readonly ?string $shippingCarrier = null,
        public readonly ?bool $allowSelfDesignAwb = null,
        public readonly ?int $parcelChargeableWeightGram = null,
        public readonly ?string $sortingGroup = null,
        public readonly ?string $groupShipmentId = null,
        #[ArrayOf(PackageItem::class)]
        public readonly ?array $itemList = null,
    ) {}
}
