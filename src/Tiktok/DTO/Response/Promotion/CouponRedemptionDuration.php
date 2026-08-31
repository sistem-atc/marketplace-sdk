<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Janela em que o cupom já resgatado pode ser USADO num pedido.
 *
 * `type` ABSOLUTE = vale start/end (epoch em SEGUNDOS). RELATIVE = ignore
 * start/end e use `relativeTime`, que é uma quantidade de DIAS contada a partir
 * do resgate — não é timestamp. A API pode mandar os três campos preenchidos
 * ao mesmo tempo; quem manda é o `type`.
 */
final class CouponRedemptionDuration implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?int $startTime = null,
        public readonly ?int $endTime = null,
        public readonly ?int $relativeTime = null,
    ) {}
}
