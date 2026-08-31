<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de
 * `POST /affiliate_seller/202605/target_collaborations/conflicts/resolve`.
 *
 * Parcial por design: leia as DUAS listas.
 */
final class TargetCollaborationConflictResolveResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(TargetCollaborationConflictResolveItem::class)]
        public readonly ?array $successItems = null,
        #[ArrayOf(TargetCollaborationConflictResolveItem::class)]
        public readonly ?array $failedItems = null,
    ) {}
}
