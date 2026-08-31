<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Tarefa que o espectador precisa cumprir na LIVE pra resgatar o cupom
 * (só existe pra cupom LIVE nos EUA e UK).
 *
 * `minWatchTime` é STRING (segundos), não int — a doc declara string.
 */
final class CouponLiveTask implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $minWatchTime = null,
    ) {}
}
