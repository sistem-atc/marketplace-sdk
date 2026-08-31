<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto + taxa que o update tentou ADICIONAR e nao conseguiu.
 */
final class TargetCollaborationProductEntry implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        // centesimos de %: 3587 = 35,87%
        public readonly ?int $commissionRate = null,
    ) {}
}
