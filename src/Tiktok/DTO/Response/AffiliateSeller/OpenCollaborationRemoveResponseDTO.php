<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de `POST /affiliate_seller/202409/open_collaborations/products/{id}`.
 *
 * A remocao NAO e' imediata: `terminatedEffectiveTime` costuma ser 00:00 do
 * dia seguinte. Ate la o creator continua promovendo.
 */
final class OpenCollaborationRemoveResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $terminatedEffectiveTime = null,
    ) {}
}
