<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Ponto escalar de serie temporal da live: `value` sempre STRING ("456"), mesmo
 * sendo contagem inteira — quirk do TikTok. `timestamp` e' epoch em SEGUNDOS.
 */
final class LiveRoomValueDataPoint implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $value = null,
        public readonly ?int $timestamp = null,
    ) {}
}
