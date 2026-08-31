<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Par creator x produto que ja esta em outro convite dirigido e por isso
 * nao entrou neste.
 */
final class TargetCollaborationConflict implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $creatorUserOpenId = null,
        public readonly ?string $productId = null,
    ) {}
}
