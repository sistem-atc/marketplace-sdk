<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Prazo de manuseio do pedido (`handling_duration`).
 *
 * `days` vem STRING, como todo numero do TikTok — nao tipar int, senao o
 * roundtrip perde o formato original.
 */
final class HandlingDuration implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $days = null,
        /** BUSINESS_DAY | CALENDAR_DAY */
        public readonly ?string $type = null,
    ) {}
}
