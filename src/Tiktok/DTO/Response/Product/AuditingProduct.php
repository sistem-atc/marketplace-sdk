<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Produto retornado pela pesquisa de auditoria (pode ser de OUTRO vendedor). */
final class AuditingProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        public readonly ?string $country = null,
        public readonly ?string $description = null,
        #[ArrayOf(AuditingVariant::class)]
        public readonly ?array $variants = null,
        public readonly ?string $listingUrl = null,
        public readonly ?string $sellerName = null,
        public readonly ?string $sellerId = null,
        public readonly ?array $mainImageUrls = null,
        public readonly ?string $brandId = null,
        public readonly ?string $brandName = null,
        #[ArrayOf(AuditingCategory::class)]
        public readonly ?array $categories = null,
    ) {}
}
