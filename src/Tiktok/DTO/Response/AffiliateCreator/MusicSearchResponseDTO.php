<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de GET /affiliate_creator/202602/music/search.
 *
 * Paginacao DIFERENTE do resto do dominio: alem do `nextPageToken` ha um
 * `searchId` que precisa ser repetido em TODA pagina seguinte da mesma busca,
 * senao a paginacao quebra. E o fim de lista e' `hasMore=false`, nao token
 * vazio.
 *
 * @property list<MusicItem>|null $music
 */
final class MusicSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(MusicItem::class)]
        public readonly ?array $music = null,
        public readonly ?string $nextPageToken = null,
        public readonly ?bool $hasMore = null,
        public readonly ?string $searchId = null,
    ) {}
}
