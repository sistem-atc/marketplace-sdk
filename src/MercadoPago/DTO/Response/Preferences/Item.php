<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference Item resource. Represents a product or service line item within a checkout
 * preference. Each item defines what the buyer is purchasing, including its title, description,
 * quantity, unit price, and optional category descriptor.
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

        /** Long item description. */
        public readonly ?string $description = null,

        /** Image URL. */
        public readonly ?string $pictureUrl = null,

        /** Category of the item. */
        public readonly ?string $categoryId = null,

        /** Item's quantity. */
        public readonly int|string|null $quantity = null,

        /** Unit price. */
        public readonly int|float|string|null $unitPrice = null,

        /** Currency ID. ISO_4217 code. */
        public readonly ?string $currencyId = null,

        /** Category Descriptor */
        public readonly ?CategoryDescriptor $categoryDescriptor = null,

        /** Whether the item includes a warranty. */
        public readonly ?bool $warranty = null,

        /** Item type. */
        public readonly ?string $type = null,

        /** ISO 8601 date of the event associated with the item. */
        public readonly ?string $eventDate = null,
    ) {}
}
