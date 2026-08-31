<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Regra de amostra do convite dirigido.
 *
 * `isSampleApprovalExempt` NAO tem efeito quando `hasFreeSample=false`.
 */
final class FreeSampleRule implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $hasFreeSample = null,
        // dispensa o creator da aprovacao manual do seller
        public readonly ?bool $isSampleApprovalExempt = null,
    ) {}
}
