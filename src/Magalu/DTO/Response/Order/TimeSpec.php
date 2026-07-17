<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Prazo (`shipping.deadline` e `shipping.handling_time` — mesma shape).
 * `value` em dias; `workday` = úteis; `limitDate` = data-limite calculada.
 */
final class TimeSpec implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $value = null,
        public readonly ?string $precision = null,
        public readonly ?bool $workday = null,
        public readonly ?string $limitDate = null,
    ) {}
}
