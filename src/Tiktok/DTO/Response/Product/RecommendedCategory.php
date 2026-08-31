<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Categoria recomendada. `permissionStatuses`: AVAILABLE (pode usar),
 * INVITE_ONLY (restrita, precisa aplicar no Qualification Center) ou
 * PROHIBITED (proibida — no BR/MX; publicar la' e' rejeicao certa).
 */
final class RecommendedCategory implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?bool $isLeaf = null,
        public readonly ?int $level = null,
        public readonly ?string $name = null,
        public readonly ?array $permissionStatuses = null,
    ) {}
}
