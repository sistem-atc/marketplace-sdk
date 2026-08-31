<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de POST /affiliate_creator/202607/photos.
 *
 * `quota` e' TEXTO ("3/day") — ver ShoppableVideoPostResponseDTO.
 */
final class ShoppablePhotoPostResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ShoppablePhotoPost $photo = null,
        public readonly ?string $quota = null,
    ) {}
}
