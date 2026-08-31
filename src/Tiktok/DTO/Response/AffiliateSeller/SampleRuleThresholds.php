<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Corte que o creator precisa passar pra pedir amostra gratis.
 *
 * `minimumGmv` e `avgEcVideoViews` olham os ultimos 30 dias.
 */
final class SampleRuleThresholds implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $minimumFollowerCount = null,
        public readonly ?int $minimumGmv = null,
        public readonly ?int $avgEcVideoViews = null,
        // categorias de 1o nivel; o creator entra se uma delas estiver no top-3 de GMV dele
        public readonly ?array $categoryIds = null,
        // ALL | LOW | MEDIUM | HIGH
        public readonly ?string $predictedFulfillmentRank = null,
    ) {}
}
