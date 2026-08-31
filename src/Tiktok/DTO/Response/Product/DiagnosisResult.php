<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Problema identificado num campo do produto.
 *
 * `code` e' machine-readable (ex.: TITLE_LESS_THAN_40_CHARACTERS) e e' por ele
 * que se automatiza; `howToSolve` vem no idioma padrao da loja.
 */
final class DiagnosisResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $code = null,
        public readonly ?string $howToSolve = null,
        public readonly ?string $qualityTier = null,
    ) {}
}
