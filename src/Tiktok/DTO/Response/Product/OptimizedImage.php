<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Imagem otimizada pela plataforma.
 *
 * `optimizedUri` e' o que se manda no Create/Edit Product — a `url` serve so'
 * pra visualizacao (e pra embutir em `<img>` na descricao).
 */
final class OptimizedImage implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $height = null,
        public readonly ?int $width = null,
        public readonly ?string $uri = null,
        public readonly ?string $url = null,
        public readonly ?string $optimizedUri = null,
        public readonly ?string $optimizedUrl = null,
    ) {}
}
