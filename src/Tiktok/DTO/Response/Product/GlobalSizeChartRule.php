<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Regra de tabela de medidas da categoria (`size_chart`).
 *
 * `isSupported=false` e' silencioso e caro: mandar tabela de medidas assim
 * mesmo NAO da' erro — a API simplesmente descarta o dado.
 */
final class GlobalSizeChartRule implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $isSupported = null,
        public readonly ?bool $isRequired = null,
    ) {}
}
