<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Condicao para destravar uma recompensa SKPP (`rewards[].claim_conditions[]`).
 *
 * `currentValue`/`targetValue` sao STRING de proposito: quando o tipo e' GMV o
 * TikTok manda o valor JA com o simbolo da moeda ("$100"), nao um numero.
 */
final class SkppClaimCondition implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $conditionType = null,
        public readonly ?string $currentValue = null,
        public readonly ?string $targetValue = null,
    ) {}
}
