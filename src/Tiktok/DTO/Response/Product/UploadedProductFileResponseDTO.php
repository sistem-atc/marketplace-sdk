<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de upload de arquivo de produto (PDF de certificacao, video).
 *
 * Identificado por `id` — diferente da imagem, que e' identificada por `uri`.
 * E' esse `id` que vai em `certifications[].files[].id` na criacao/edicao.
 */
final class UploadedProductFileResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $url = null,
        public readonly ?string $name = null,
        public readonly ?string $format = null,
    ) {}
}
