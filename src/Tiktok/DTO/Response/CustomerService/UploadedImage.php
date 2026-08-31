<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Imagem hospedada pelo TikTok pro chat de atendimento.
 *
 * A `url` devolvida é o que se manda em sendMessage(type: 'IMAGE'); a imagem
 * local nunca é enviada na mensagem. Dimensões vêm em pixels.
 */
final class UploadedImage implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $url = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
    ) {}
}
