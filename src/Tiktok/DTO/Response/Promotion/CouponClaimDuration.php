<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Janela em que o cupom pode ser RESGATADO pelo comprador. Epoch em SEGUNDOS. */
final class CouponClaimDuration implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $startTime = null,
        public readonly ?int $endTime = null,
    ) {}
}
