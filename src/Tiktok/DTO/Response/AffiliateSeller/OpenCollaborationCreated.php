<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Colaboracao aberta recem-criada. `effectiveTime` e' quando a comissao passa
 * a valer (normalmente nao e' agora).
 */
final class OpenCollaborationCreated implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $productId = null,
        public readonly ?int $effectiveTime = null,
    ) {}
}
