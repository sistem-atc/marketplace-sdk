<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Arquivo de foto ja' no storage do TikTok. O `photoUri` e' o que entra em
 * `photos_info[].photo_file_uris` do Post Shoppable Photos.
 */
final class PhotoFile implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $photoUri = null,
    ) {}
}
