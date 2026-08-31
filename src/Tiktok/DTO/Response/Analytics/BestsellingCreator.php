<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.creators[]` do /analytics/{v}/creators/bestselling.
 *
 * NAO ha GMV absoluto: o TikTok so' publica FAIXA (`gmvRange`, "$97K ~ $191K") no
 * ranking publico de bestsellers. Quem precisa do valor exato tem que usar os
 * endpoints de performance da propria loja.
 */
final class BestsellingCreator implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $rank = null,
        public readonly ?string $gmvRange = null,
        public readonly ?string $openId = null,
        public readonly ?string $userName = null,
        public readonly ?string $nickName = null,
        public readonly ?int $followersCount = null,
        public readonly ?int $likesCount = null,
    ) {}
}
