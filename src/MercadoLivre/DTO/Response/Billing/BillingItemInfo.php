<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Billing;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `items_info[]` — anúncio(s) da cobrança.
 */
final class BillingItemInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $itemId = null,
        public readonly ?string $itemKitId = null,
        public readonly ?string $itemTitle = null,
        public readonly ?string $itemType = null,
        public readonly ?string $itemCategory = null,
        public readonly ?string $inventoryId = null,
        public readonly ?float $itemAmount = null,
        public readonly ?float $itemPrice = null,
        public readonly ?int $orderId = null,
    ) {}
}
