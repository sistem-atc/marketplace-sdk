<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Fatia do GMV do creator por tipo de conteudo.
 */
final class ContentGmvShare implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // VIDEO | LIVE | SHOWCASE
        public readonly ?string $contentType = null,
        // fracao: "0.3035" = 30,35%
        public readonly ?string $value = null,
    ) {}
}
