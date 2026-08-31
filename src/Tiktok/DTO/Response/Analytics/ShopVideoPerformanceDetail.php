<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.performance` do /analytics/{v}/shop_videos/{video_id}/performance.
 *
 * `viewerProfile` e' do PERIODO INTEIRO consultado, nao por intervalo.
 */
final class ShopVideoPerformanceDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ShopVideoDetailInterval::class)]
        public readonly ?array $intervals = null,
        #[ArrayOf(ShopVideoViewerProfile::class)]
        public readonly ?array $viewerProfile = null,
    ) {}
}
