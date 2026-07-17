<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Anúncio Shopee — item de `response.item_list[]` do
 * `/api/v2/product/get_item_base_info`.
 *
 * GOTCHA DO PREÇO: anúncio com variação (`hasModel = true`) NÃO traz
 * `priceInfo` nem `stockInfoV2` aqui — preço e estoque moram por variação e
 * só vêm no `getModelList($itemId)`. Ler preço direto daqui só funciona pra
 * anúncio sem variação; pra variação, `priceInfo` null NÃO significa
 * "sem preço".
 *
 * `itemSku` é o SKU do vendedor no anúncio-pai (de/para com o catálogo);
 * a variação tem o dela em `Model::$modelSku`.
 *
 * Datas são epoch em SEGUNDOS.
 *
 * @property list<PriceInfo>|null $priceInfo
 * @property list<LogisticInfo>|null $logisticInfo
 * @property list<ItemAttribute>|null $attributeList
 * @property list<VideoInfo>|null $videoInfo
 * @property array<int, mixed>|null $compatibilityInfo
 */
final class ItemResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $itemId = null,
        public readonly ?string $itemName = null,
        public readonly ?string $itemSku = null,
        public readonly ?string $itemStatus = null,
        public readonly ?int $categoryId = null,
        public readonly ?string $description = null,
        public readonly ?string $descriptionType = null,
        public readonly ?DescriptionInfo $descriptionInfo = null,
        public readonly ?string $condition = null,
        // Epoch em SEGUNDOS.
        public readonly ?int $createTime = null,
        public readonly ?int $updateTime = null,
        // `weight` vem como STRING na Shopee (ex.: "0.9"), não número.
        public readonly ?string $weight = null,
        public readonly ?Dimension $dimension = null,
        public readonly ?Brand $brand = null,
        public readonly ?int $authorisedBrandId = null,
        public readonly ?ItemImage $image = null,
        public readonly ?ItemImage $promotionImage = null,
        public readonly ?bool $hasModel = null,
        public readonly ?bool $hasPromotion = null,
        public readonly ?int $promotionId = null,
        public readonly ?bool $isFulfillmentByShopee = null,
        public readonly ?PreOrder $preOrder = null,
        public readonly ?PurchaseLimitInfo $purchaseLimitInfo = null,
        public readonly ?ItemTag $tag = null,
        public readonly ?string $gtinCode = null,
        public readonly ?int $itemDangerous = null,
        public readonly ?string $deboost = null,
        public readonly ?string $sizeChart = null,
        public readonly ?int $sizeChartId = null,
        public readonly ?StockInfoV2 $stockInfoV2 = null,
        #[ArrayOf(PriceInfo::class)]
        public readonly ?array $priceInfo = null,
        #[ArrayOf(LogisticInfo::class)]
        public readonly ?array $logisticInfo = null,
        #[ArrayOf(ItemAttribute::class)]
        public readonly ?array $attributeList = null,
        #[ArrayOf(VideoInfo::class)]
        public readonly ?array $videoInfo = null,
        // Sempre vazio no corpus BR; a Shopee não documenta a shape interna.
        public readonly ?array $compatibilityInfo = null,
    ) {}
}
