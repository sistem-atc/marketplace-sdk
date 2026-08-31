<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * GPM = GMV por MIL. `watchGpm` usa views como base; `showGpm`, impressoes.
 * Em Shop LIVE Products Performance so' `watchGpm` e' devolvido.
 */
final class LiveGpm implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $watchGpm = null,
        public readonly ?string $showGpm = null,
    ) {}
}
