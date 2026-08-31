<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Recomendacao de pacote de um produto (`products[]`).
 *
 * `weightRecommendation`/`dimensionRecommendation` so' vem quando o TikTok
 * detectou anomalia naquele eixo — `abnormalityTypes` diz quais ("Dimension",
 * "Weight"). Peso/dimensao errados encarecem o frete cobrado do vendedor.
 */
final class PackageRecommendationProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        public readonly ?array $abnormalityTypes = null,
        public readonly ?WeightRecommendation $weightRecommendation = null,
        public readonly ?DimensionRecommendation $dimensionRecommendation = null,
    ) {}
}
