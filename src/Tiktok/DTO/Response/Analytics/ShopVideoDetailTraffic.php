<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Bloco `traffic` do intervalo do detalhe de video — engajamento puro, sem
 * metrica de produto (essas ficam em `ShopVideoSalesMetrics`).
 * `views` conta REPETICOES do mesmo usuario.
 */
final class ShopVideoDetailTraffic implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $views = null,
        public readonly ?int $newFollowers = null,
        public readonly ?int $shares = null,
        public readonly ?int $comments = null,
        public readonly ?int $likes = null,
    ) {}
}
