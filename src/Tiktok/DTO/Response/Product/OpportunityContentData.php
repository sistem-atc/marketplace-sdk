<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Sinais de conteudo dos ultimos 30 dias.
 *
 * Os numeros vem como STRING JA formatada e arredondada pelo TikTok ("7k+",
 * "336k+") — nao da' pra somar nem comparar sem parsear o sufixo.
 */
final class OpportunityContentData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $videoViewPv = null,
        public readonly ?string $shoppableVideoSearchPv = null,
        public readonly ?string $trendingHashtag = null,
        public readonly ?array $topShoppableVideoLinks = null,
    ) {}
}
