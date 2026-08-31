<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto dentro de uma colaboracao DIRECIONADA (convite do vendedor).
 */
final class TargetCollaborationProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        public readonly ?string $mainImageUrl = null,
        public readonly ?ProductCommission $commission = null,
    ) {}
}
