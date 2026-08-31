<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Fonte de trafego da live (nome + views). Mesmo shape para a fonte principal e
 * para as sub-fontes, entao e' o mesmo DTO nos dois lugares.
 */
final class LiveRoomTrafficSource implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?int $watchPv = null,
    ) {}
}
