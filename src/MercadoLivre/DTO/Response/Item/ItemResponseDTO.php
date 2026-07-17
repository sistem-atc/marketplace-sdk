<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * RAIZ da resposta de GET /items/{id} (ItemMethods::get / multiGet).
 *
 * Ponto ÚNICO de parse do anúncio: preço, estoque, status, SKU, atributos,
 * variações, frete. `toArray()` e' LOSSLESS — cobre 100% do payload.
 *
 * @property list<ItemAttribute> $attributes
 * @property list<ItemAttribute> $saleTerms
 * @property list<ItemPicture> $pictures
 * @property list<ItemVariation> $variations
 * @property array<int|string, mixed> $tags
 * @property array<int|string, mixed> $subStatus
 * @property array<int|string, mixed> $warnings
 * @property array<int|string, mixed> $dealIds
 * @property array<int|string, mixed> $descriptions
 * @property array<int|string, mixed> $channels
 * @property array<int|string, mixed> $itemRelations
 * @property array<int|string, mixed> $coverageAreas
 * @property array<int|string, mixed> $nonMercadoPagoPaymentMethods
 */
final class ItemResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<ItemAttribute>  $attributes
     * @param  list<ItemAttribute>  $saleTerms
     * @param  list<ItemPicture>  $pictures
     * @param  list<ItemVariation>  $variations
     * @param  array<int|string, mixed>  $tags
     * @param  array<int|string, mixed>  $subStatus
     * @param  array<int|string, mixed>  $warnings
     * @param  array<int|string, mixed>  $dealIds
     * @param  array<int|string, mixed>  $descriptions
     * @param  array<int|string, mixed>  $channels
     * @param  array<int|string, mixed>  $itemRelations
     * @param  array<int|string, mixed>  $coverageAreas
     * @param  array<int|string, mixed>  $nonMercadoPagoPaymentMethods
     */
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $siteId = null,
        public readonly ?string $title = null,
        public readonly ?string $status = null,
        public readonly ?string $permalink = null,
        public readonly ?string $categoryId = null,
        public readonly ?string $domainId = null,
        public readonly ?int $sellerId = null,
        public readonly ?int $officialStoreId = null,
        // preço
        public readonly ?float $price = null,
        public readonly ?float $basePrice = null,
        public readonly ?float $originalPrice = null,
        public readonly ?string $currencyId = null,
        // estoque
        public readonly ?int $initialQuantity = null,
        public readonly ?int $availableQuantity = null,
        public readonly ?int $soldQuantity = null,
        public readonly ?string $inventoryId = null,
        // identificação/SKU
        public readonly ?string $sellerCustomField = null,
        public readonly ?string $userProductId = null,
        public readonly ?string $catalogProductId = null,
        public readonly ?string $parentItemId = null,
        public readonly ?string $familyId = null,
        public readonly ?string $familyName = null,
        // anúncio
        public readonly ?string $buyingMode = null,
        public readonly ?string $listingTypeId = null,
        public readonly ?string $listingSource = null,
        public readonly ?string $condition = null,
        public readonly ?string $warranty = null,
        public readonly ?bool $catalogListing = null,
        public readonly ?bool $automaticRelist = null,
        public readonly ?bool $acceptsMercadopago = null,
        public readonly mixed $health = null,
        // mídia
        public readonly ?string $thumbnail = null,
        public readonly ?string $thumbnailId = null,
        public readonly ?string $videoId = null,
        // datas
        public readonly ?string $startTime = null,
        public readonly ?string $stopTime = null,
        public readonly ?string $endTime = null,
        public readonly ?string $expirationTime = null,
        public readonly ?string $dateCreated = null,
        public readonly ?string $lastUpdated = null,
        // blocos tipados
        public readonly ?ItemShipping $shipping = null,
        #[ArrayOf(ItemAttribute::class)]
        public readonly array $attributes = [],
        #[ArrayOf(ItemAttribute::class)]
        public readonly array $saleTerms = [],
        #[ArrayOf(ItemPicture::class)]
        public readonly array $pictures = [],
        #[ArrayOf(ItemVariation::class)]
        public readonly array $variations = [],
        // blocos crus (shape volátil / raramente lidos)
        public readonly mixed $sellerAddress = null,
        public readonly mixed $sellerContact = null,
        public readonly mixed $location = null,
        public readonly mixed $geolocation = null,
        public readonly mixed $differentialPricing = null,
        public readonly mixed $internationalDeliveryMode = null,
        public readonly array $tags = [],
        public readonly array $subStatus = [],
        public readonly array $warnings = [],
        public readonly array $dealIds = [],
        public readonly array $descriptions = [],
        public readonly array $channels = [],
        public readonly array $itemRelations = [],
        public readonly array $coverageAreas = [],
        public readonly array $nonMercadoPagoPaymentMethods = [],
    ) {}
}
