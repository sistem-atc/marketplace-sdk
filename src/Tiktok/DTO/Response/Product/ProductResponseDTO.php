<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto TikTok. Serve as DUAS respostas:
 *   - search (`products/search`) — enxuto: id, title, status, skus (c/ preço)
 *   - get    (`products/{id}`)    — detalhe COMPLETO
 *
 * PARCIAL POR SHAPE (não por decisão): o search preenche um subconjunto; o
 * resto (brand, category_chains, certifications, images, description, video,
 * subscribe_info, dimensões...) só vem no get. Campo null pode ser "veio do
 * search", não "não existe".
 *
 * O detalhe é mapeado POR INTEIRO — a finalidade da refatoração pra DTO é ter
 * todo dado da API disponível, independente do uso atual (a API pode suprimir
 * campos noutras fontes; aqui não). IDs são STRING (snowflake); datas = epoch
 * em SEGUNDOS.
 *
 * @property list<ProductSku>|null $skus
 * @property list<ProductAttribute>|null $productAttributes
 * @property list<CategoryChain>|null $categoryChains
 * @property list<Certification>|null $certifications
 * @property list<Image>|null $mainImages
 * @property list<string>|null $manufacturerIds
 * @property list<string>|null $responsiblePersonIds
 * @property list<string>|null $productTags
 * @property list<string>|null $salesRegions
 * @property list<string>|null $recommendedCategories
 */
final class ProductResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        public readonly ?string $status = null,
        public readonly ?string $productStatus = null,
        public readonly ?int $createTime = null,
        public readonly ?int $updateTime = null,
        public readonly ?string $description = null,
        public readonly ?string $externalProductId = null,
        public readonly ?Brand $brand = null,
        public readonly ?Audit $audit = null,
        public readonly ?Video $video = null,
        public readonly ?SubscribeInfo $subscribeInfo = null,
        public readonly ?Dimensions $packageDimensions = null,
        public readonly ?Weight $packageWeight = null,
        public readonly ?string $shippingInsuranceRequirement = null,
        // Flags
        public readonly ?bool $hasDraft = null,
        public readonly ?bool $isCodAllowed = null,
        public readonly ?bool $isNotForSale = null,
        public readonly ?bool $isPreOwned = null,
        public readonly ?bool $isReplicated = null,
        // Listas de escalares (passam intactas)
        public readonly ?array $manufacturerIds = null,
        public readonly ?array $responsiblePersonIds = null,
        public readonly ?array $productTags = null,
        public readonly ?array $salesRegions = null,
        public readonly ?array $recommendedCategories = null,
        // Listas de DTO
        #[ArrayOf(ProductSku::class)]
        public readonly ?array $skus = null,
        #[ArrayOf(ProductAttribute::class)]
        public readonly ?array $productAttributes = null,
        #[ArrayOf(CategoryChain::class)]
        public readonly ?array $categoryChains = null,
        #[ArrayOf(Certification::class)]
        public readonly ?array $certifications = null,
        #[ArrayOf(Image::class)]
        public readonly ?array $mainImages = null,
    ) {}
}
