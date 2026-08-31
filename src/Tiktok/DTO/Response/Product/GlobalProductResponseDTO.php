<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto GLOBAL do TikTok Shop (catalogo cross-border).
 *
 * Serve as DUAS respostas, como o ProductResponseDTO local:
 *   - search (`global_products/search`) — enxuto: id, title, status, skus
 *     (so' id + seller_sku), create/update_time
 *   - get    (`global_products/{id}`)    — detalhe COMPLETO
 *
 * NAO confunda com ProductResponseDTO (produto LOCAL): o global nao tem
 * shop, nao tem preco local e nao vende — ele e' o molde que, ao ser
 * publicado (Publish Global Product), GERA um produto local por mercado.
 * Os produtos gerados aparecem em `products`, com o de/para de SKU.
 *
 * Campo null pode ser "veio do search", nao "nao existe".
 * IDs sao STRING (snowflake); datas sao epoch em SEGUNDOS.
 *
 * @property list<GlobalImage>|null $mainImages
 * @property list<GlobalCertification>|null $certifications
 * @property list<GlobalProductSku>|null $skus
 * @property list<GlobalProductAttribute>|null $productAttributes
 * @property list<GlobalLocalProduct>|null $products
 * @property list<string>|null $responsiblePersonIds
 * @property list<string>|null $manufacturerIds
 */
final class GlobalProductResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        // So no search: PUBLISHED | UNPUBLISHED | DRAFT | DELETED.
        public readonly ?string $status = null,
        public readonly ?string $description = null,
        public readonly ?int $createTime = null,
        public readonly ?int $updateTime = null,
        public readonly ?string $globalSellerId = null,
        public readonly ?string $sourceLocale = null,
        public readonly ?string $externalGlobalProductId = null,
        public readonly ?GlobalProductVideo $video = null,
        public readonly ?GlobalPackageDimensions $packageDimensions = null,
        public readonly ?GlobalPackageWeight $packageWeight = null,
        public readonly ?GlobalSizeChart $sizeChart = null,
        public readonly ?GlobalBrandRef $brand = null,
        public readonly ?GlobalCategoryRef $category = null,
        // Depreciado pela API — ver GlobalManufacturerContact.
        public readonly ?GlobalManufacturerContact $manufacturer = null,
        // So mercado UE, em certas categorias.
        public readonly ?array $responsiblePersonIds = null,
        public readonly ?array $manufacturerIds = null,
        #[ArrayOf(GlobalImage::class)]
        public readonly ?array $mainImages = null,
        #[ArrayOf(GlobalCertification::class)]
        public readonly ?array $certifications = null,
        #[ArrayOf(GlobalProductSku::class)]
        public readonly ?array $skus = null,
        #[ArrayOf(GlobalProductAttribute::class)]
        public readonly ?array $productAttributes = null,
        // Produtos LOCAIS gerados/vinculados a este global.
        #[ArrayOf(GlobalLocalProduct::class)]
        public readonly ?array $products = null,
    ) {}
}
