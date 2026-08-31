<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Faixa permitida pro reembolso parcial (endpoint Get Review Decision).
 *
 * Propor fora da faixa e' rejeitado pela API — vale validar antes de mandar.
 */
final class PartialRefundAmountRange implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $minAmount = null,
        public readonly ?string $maxAmount = null,
        public readonly ?string $currency = null,
    ) {}
}
