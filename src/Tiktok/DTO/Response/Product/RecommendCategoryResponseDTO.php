<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /product/202309/categories/recommend`.
 *
 * `categories` e' a CADEIA da raiz ate' a folha; `leafCategoryId` e' o que se
 * usa no Create Product (produto so' entra em categoria folha).
 */
final class RecommendCategoryResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(RecommendedCategory::class)]
        public readonly ?array $categories = null,
        public readonly ?string $leafCategoryId = null,
    ) {}
}
