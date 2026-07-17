<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/seller/v1/portfolios/skus` — envelope `results` + `meta`,
 * paginação por offset (`meta.page.offset`).
 *
 * @property list<PortfolioSku>|null $results
 */
final class SkuListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(PortfolioSku::class)]
        public readonly ?array $results = null,
        public readonly ?Meta $meta = null,
    ) {}
}
