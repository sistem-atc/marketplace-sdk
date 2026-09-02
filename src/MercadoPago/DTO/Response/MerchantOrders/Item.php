<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Merchant Order Item resource. Represents a product or service line item within a merchant
 * order. Each item includes its title, description, quantity, unit price, and currency
 * information.
 */
final class Item implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Item code. */
        public readonly ?string $id = null,

        /** Item name. */
        public readonly ?string $title = null,

        /** Item description. */
        public readonly ?string $description = null,

        /** Image URL. */
        public readonly ?string $pictureUrl = null,

        /** Category of the item. */
        public readonly ?string $categoryId = null,

        /** Item's quantity. */
        public readonly ?int $quantity = null,

        /** Unit price. */
        public readonly ?float $unitPrice = null,

        /** Currency ID. ISO_4217 code. */
        public readonly ?string $currencyId = null,
    ) {}
}
