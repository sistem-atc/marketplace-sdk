<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.videos[]` do /analytics/202403/videos/performances.
 *
 * Endpoint de CREATOR (todos os video_ids da consulta precisam ser do MESMO
 * autor, limite 100). Nao confundir com `ShopVideoPerformanceSummary`, que e' a
 * visao da LOJA e traz nomes de metrica completamente diferentes.
 */
final class VideoPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        #[ArrayOf(VideoPerformanceEntry::class)]
        public readonly ?array $performances = null,
    ) {}
}
