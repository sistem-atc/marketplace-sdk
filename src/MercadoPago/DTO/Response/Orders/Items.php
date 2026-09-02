<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a line item within a MercadoPago order. Each item describes a product or service
 * being purchased, including its price, quantity, and categorization. The sum of all item amounts
 * should match the order's total amount.
 */
final class Items implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier of the item within the order. */
        public readonly ?string $id = null,

        /** Display name of the product or service. */
        public readonly ?string $title = null,

        /** Price per unit in the order's currency. */
        public readonly ?string $unitPrice = null,

        /** Number of units of this item being purchased. */
        public readonly ?int $quantity = null,

        /** Unit of measure for the item (e.g., "unit", "kg"). */
        public readonly ?string $unitMeasure = null,

        /** Seller-defined code to identify the item in an external system (e.g., SKU). */
        public readonly ?string $externalCode = null,

        /** External category classifications for the item. Each element maps to ExternalCategory. @var list<ExternalCategory>|null */
        #[ArrayOf(ExternalCategory::class)]
        public readonly ?array $externalCategories = null,

        /** MercadoPago category identifier used for fraud analysis and processing rules. */
        public readonly ?string $categoryId = null,

        /** Detailed description of the product or service. */
        public readonly ?string $description = null,

        /** URL of the item's product image. */
        public readonly ?string $pictureUrl = null,

        /** Item type classification (e.g., "physical", "digital", "service"). */
        public readonly ?string $type = null,

        /** Whether the item includes a warranty. */
        public readonly ?bool $warranty = null,

        /** ISO 8601 date of the event associated with the item (e.g., for ticket sales). */
        public readonly ?string $eventDate = null,
    ) {}
}
