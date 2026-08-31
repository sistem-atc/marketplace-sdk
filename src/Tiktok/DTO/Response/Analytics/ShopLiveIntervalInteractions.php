<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Interacoes do intervalo. As quatro taxas (`*Rate`) tem VIEWS no denominador,
 * nao viewers. Fracao STRING.
 */
final class ShopLiveIntervalInteractions implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $newFollowers = null,
        public readonly ?int $shares = null,
        public readonly ?int $comments = null,
        public readonly ?int $likes = null,
        public readonly ?string $commentRate = null,
        public readonly ?string $followRate = null,
        public readonly ?string $likeRate = null,
        public readonly ?string $shareRate = null,
    ) {}
}
