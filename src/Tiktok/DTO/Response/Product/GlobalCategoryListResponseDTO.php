<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `global_categories` — a arvore INTEIRA de uma vez (nao pagina).
 * A hierarquia se remonta por `parentId`.
 *
 * @property list<GlobalCategory>|null $categories
 */
final class GlobalCategoryListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(GlobalCategory::class)]
        public readonly ?array $categories = null,
    ) {}
}
