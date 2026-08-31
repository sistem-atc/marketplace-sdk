<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Texto sugerido pela IA do TikTok. Mesma shape em `seo_words[]`,
 * `smart_texts[]` e `suggestions[].items[]` — um unico campo `text`.
 */
final class SuggestionText implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $text = null,
    ) {}
}
