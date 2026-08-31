<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /product/202404/images/optimize`.
 *
 * Hoje o unico modo e' WHITE_BACKGROUND (fundo branco), exigido pela 1a imagem
 * da galeria. Max 200 imagens por chamada.
 */
final class OptimizedImagesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(OptimizedImageResult::class)]
        public readonly ?array $images = null,
    ) {}
}
