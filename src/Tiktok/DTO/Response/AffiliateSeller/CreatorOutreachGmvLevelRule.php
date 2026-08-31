<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Regra de cota por faixa de GMV da loja: dentro de [leftGmv, rightGmv) a loja
 * pode abordar creators com ate `reachedMaxFollowerCnt` seguidores.
 */
final class CreatorOutreachGmvLevelRule implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $reachedMaxFollowerCnt = null,
        public readonly ?string $gmvTier = null,
        public readonly ?AffiliateMoney $leftGmv = null,
        public readonly ?AffiliateMoney $rightGmv = null,
    ) {}
}
