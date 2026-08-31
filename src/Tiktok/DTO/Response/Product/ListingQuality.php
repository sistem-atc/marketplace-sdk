<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Nivel de qualidade do anuncio (POOR / FAIR / GOOD).
 *
 * So' o mercado US preenche isso — no BR vem vazio.
 */
final class ListingQuality implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $currentTier = null,
        public readonly ?int $remainingRecommendations = null,
    ) {}
}
