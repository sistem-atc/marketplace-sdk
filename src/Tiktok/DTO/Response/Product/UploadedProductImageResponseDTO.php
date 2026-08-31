<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de upload de imagem de produto.
 *
 * Guarde a `uri` (e' ela que vai em `main_images`/`sku_img`); a `url` e' so'
 * pra exibir e pra embutir em <img> na descricao.
 *
 * `height`/`width` sao os APOS o ajuste de proporcao feito pelo TikTok — nao
 * os do arquivo enviado: imagem MAIN/ATTRIBUTE fora da faixa 3:4~4:3 e'
 * convertida pra 1:1 automaticamente.
 */
final class UploadedProductImageResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $uri = null,
        public readonly ?string $url = null,
        public readonly ?int $height = null,
        public readonly ?int $width = null,
        public readonly ?string $useCase = null,
    ) {}
}
