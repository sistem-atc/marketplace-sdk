<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de `/affiliate_seller/202511/images/upload` e da V2
 * `/affiliate_seller/202608/media/upload`.
 *
 * A `url` daqui e' o que entra no `content` de uma mensagem IMAGE — junto com
 * `width`/`height`, que o TikTok exige no card.
 */
final class MessageImageUploadResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $url = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
    ) {}
}
