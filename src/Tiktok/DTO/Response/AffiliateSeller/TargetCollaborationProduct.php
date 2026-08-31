<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto dentro do convite dirigido.
 *
 * `collaborationStatus=DELETING` = sai as 00:00 do dia seguinte; ate la conta
 * como ativo.
 */
final class TargetCollaborationProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $mainImageUrl = null,
        public readonly ?string $title = null,
        public readonly ?AffiliatePriceRange $originalPrice = null,
        public readonly ?TargetCollaborationProductCommission $commission = null,
        // LIVE | OUT_OF_STOCK | SELLER_DEACTIVATE | PLATFORM_DEACTIVATE | GNE_REJECT | DELETE | OTHER
        public readonly ?string $status = null,
        // EFFECTIVE_ALL | EFFECTIVE_PARTIALLY | EFFECTIVE_NONE
        public readonly ?string $commissionEffectiveStatus = null,
        // NORMAL | DELETING | DELETED
        public readonly ?string $collaborationStatus = null,
    ) {}
}
