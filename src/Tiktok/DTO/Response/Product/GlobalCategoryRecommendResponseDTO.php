<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `global_categories/recommend`.
 *
 * `leafCategoryId` e' o atalho: ja' vem a folha recomendada (a unica que
 * aceita produto). `categories` e' o caminho completo ate' ela.
 *
 * @property list<GlobalRecommendedCategory>|null $categories
 */
final class GlobalCategoryRecommendResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $leafCategoryId = null,
        #[ArrayOf(GlobalRecommendedCategory::class)]
        public readonly ?array $categories = null,
    ) {}
}
