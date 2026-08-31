<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Trafego do produto por tipo de conteudo.
 *
 * `impressions` = aparicoes do link na aba Shop; `pageViews` = views da pagina do
 * produto (com repeticao); `avgUniquePageViews` = media DIARIA de visitantes
 * unicos. Tres coisas distintas com nomes proximos.
 */
final class ShopProductBreakdownTraffic implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $impressions = null,
        public readonly ?int $pageViews = null,
        public readonly ?int $avgUniquePageViews = null,
        public readonly ?string $ctr = null,
        public readonly ?string $avgConversionRate = null,
    ) {}
}
