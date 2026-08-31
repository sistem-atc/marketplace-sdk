<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Resposta de `POST /product/202602/packages/recommend`. Pagina por page_token na QUERY. */
final class RecommendedProductPackageResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
        #[ArrayOf(PackageRecommendationProduct::class)]
        public readonly ?array $products = null,
    ) {}
}
