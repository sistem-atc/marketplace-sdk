<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Category;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Predição de categoria a partir do título (GET /sites/{site}/domain_discovery/
 * search). O ML devolve LISTA ordenada por confiança — o [0] é o melhor palpite.
 *
 * @property array<int|string, mixed> $attributes
 */
final class CategoryPrediction implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int|string, mixed> $attributes */
    public function __construct(
        public readonly ?string $categoryId = null,
        public readonly ?string $categoryName = null,
        public readonly ?string $domainId = null,
        public readonly ?string $domainName = null,
        public readonly array $attributes = [],
    ) {}
}
