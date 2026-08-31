<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Faixa da biblioteca de musica — item de `music[]`.
 *
 * `duration` vem como STRING de segundos ("235"), nao int.
 */
final class MusicItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        public readonly ?string $author = null,
        public readonly ?MusicUrlList $coverThumb = null,
        public readonly ?string $duration = null,
        public readonly ?MusicUrlList $playUrl = null,
    ) {}
}
