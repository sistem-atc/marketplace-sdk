<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado do upload de imagem de comprovacao de entrega.
 *
 * `height`/`width` sao da imagem JA PROCESSADA pelo TikTok (pixels), nao da
 * original enviada.
 */
final class UploadDeliveryImageResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $url = null,
        public readonly ?int $height = null,
        public readonly ?int $width = null,
    ) {}
}
