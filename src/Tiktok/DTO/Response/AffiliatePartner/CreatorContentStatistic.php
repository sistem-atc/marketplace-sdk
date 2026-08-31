<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Métricas de um conteúdo (vídeo ou live) do criador.
 *
 * `content_type` muda o significado de metade do objeto: em VIDEO não há
 * `cover_img_url` e `content_end_date` vem vazio; em LIVE_ROOM `published_date`
 * é o início da live e `source_url` aponta pra sala. Datas aqui são STRING
 * `YYYY-MM-DD`, não epoch.
 */
final class CreatorContentStatistic implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** VIDEO | LIVE_ROOM */
        public readonly ?string $contentType = null,
        public readonly ?string $coverImgUrl = null,
        public readonly ?string $sourceUrl = null,
        /** Contadores vêm como STRING. */
        public readonly ?string $viewCount = null,
        public readonly ?string $likeCount = null,
        public readonly ?string $commentNum = null,
        public readonly ?string $paidOrderNum = null,
        public readonly ?string $paidAmount = null,
        public readonly ?string $linkedTiktokVideo = null,
        public readonly ?string $publishedDate = null,
        public readonly ?string $contentEndDate = null,
    ) {}
}
