<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Sugestoes auto-geradas para um campo. So' vem se `optimization_fields` foi
 * pedido (default NONE). Apenas a PRIMEIRA imagem principal e' otimizada.
 */
final class DiagnosisSuggestion implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(SuggestionText::class)]
        public readonly ?array $seoWords = null,
        #[ArrayOf(SuggestionText::class)]
        public readonly ?array $smartTexts = null,
        #[ArrayOf(OptimizedImage::class)]
        public readonly ?array $images = null,
    ) {}
}
