<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item do resultado de resolucao de conflito (sucesso ou falha).
 *
 * Nao traz `productId` — a resolucao e' por (creator, colaboracao existente).
 */
final class TargetCollaborationConflictResolveItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $creatorOpenId = null,
        public readonly ?string $existingCollaborationId = null,
    ) {}
}
