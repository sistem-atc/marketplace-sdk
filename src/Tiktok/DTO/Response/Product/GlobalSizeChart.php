<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Tabela de medidas do produto global. Pode vir como IMAGEM, como TEMPLATE,
 * ou os dois — sao caminhos independentes.
 */
final class GlobalSizeChart implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?GlobalImage $image = null,
        public readonly ?GlobalSizeChartTemplate $template = null,
    ) {}
}
