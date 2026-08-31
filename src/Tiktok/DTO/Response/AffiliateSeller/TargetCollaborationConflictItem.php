<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conflito detectado: o par creator x produto ja pertence a
 * `existingCollaborationId`.
 */
final class TargetCollaborationConflictItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $creatorOpenId = null,
        public readonly ?string $productId = null,
        public readonly ?string $existingCollaborationId = null,
    ) {}
}
