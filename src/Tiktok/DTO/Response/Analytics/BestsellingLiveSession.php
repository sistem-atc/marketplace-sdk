<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.lives[]` do /analytics/{v}/lives/bestselling.
 *
 * `startTime` e' epoch em SEGUNDOS; `duration` tambem e' em segundos.
 * GMV so' vem como faixa (`gmvRange`).
 */
final class BestsellingLiveSession implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $rank = null,
        public readonly ?string $gmvRange = null,
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        public readonly ?int $startTime = null,
        public readonly ?int $duration = null,
        public readonly ?string $creatorName = null,
        public readonly ?string $openId = null,
        public readonly ?string $creatorNickName = null,
    ) {}
}
