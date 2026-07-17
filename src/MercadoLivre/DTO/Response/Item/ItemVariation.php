<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Variação do anúncio (`variations[]`) — preço/estoque/SKU próprios por combinação
 * de atributos (`attribute_combinations`, mesma shape de `attributes`).
 *
 * @property list<ItemAttribute> $attributeCombinations
 * @property list<ItemAttribute> $saleTerms
 * @property array<int|string, mixed> $pictureIds
 * @property array<int|string, mixed> $itemRelations
 */
final class ItemVariation implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<ItemAttribute>  $attributeCombinations
     * @param  list<ItemAttribute>  $saleTerms
     * @param  array<int|string, mixed>  $pictureIds
     * @param  array<int|string, mixed>  $itemRelations
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?float $price = null,
        public readonly ?int $availableQuantity = null,
        public readonly ?int $soldQuantity = null,
        public readonly ?string $sellerCustomField = null,
        public readonly ?string $inventoryId = null,
        public readonly ?string $userProductId = null,
        public readonly ?string $catalogProductId = null,
        #[ArrayOf(ItemAttribute::class)]
        public readonly array $attributeCombinations = [],
        #[ArrayOf(ItemAttribute::class)]
        public readonly array $saleTerms = [],
        public readonly array $pictureIds = [],
        public readonly array $itemRelations = [],
    ) {}
}
