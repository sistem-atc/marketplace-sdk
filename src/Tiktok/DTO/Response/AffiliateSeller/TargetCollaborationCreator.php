<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Creator dentro do convite dirigido.
 */
final class TargetCollaborationCreator implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $username = null,
        public readonly ?string $nickname = null,
        public readonly ?AffiliateImage $avatar = null,
        // regiao onde o creator pode promover
        public readonly ?string $selectionRegion = null,
        public readonly ?int $showcaseProductCount = null,
        public readonly ?int $contentProductCount = null,
        // NORMAL | DELETING | DELETED
        public readonly ?string $collaborationStatus = null,
        // EFFECTIVE_ALL | EFFECTIVE_PARTIALLY | EFFECTIVE_NONE
        public readonly ?string $productEffectiveStatus = null,
        public readonly ?string $creatorOpenId = null,
    ) {}
}
