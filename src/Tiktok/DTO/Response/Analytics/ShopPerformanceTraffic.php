<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Trafego do intervalo da loja. Tudo com prefixo `avg` e' media DIARIA.
 *
 * `avgConversationRate` e' TYPO da API pra "conversion rate" — mantido igual a'
 * chave crua de proposito.
 */
final class ShopPerformanceTraffic implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $avgPageViews = null,
        public readonly ?int $avgVisitors = null,
        public readonly ?string $avgConversationRate = null,
    ) {}
}
