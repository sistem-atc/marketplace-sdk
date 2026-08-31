<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de
 * `POST /affiliate_seller/202605/target_collaborations/conflicts/check`.
 *
 * Rode ANTES de criar: o create nao falha por conflito, ele silenciosamente
 * deixa o par de fora.
 */
final class TargetCollaborationConflictCheckResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $hasConflict = null,
        #[ArrayOf(TargetCollaborationConflictItem::class)]
        public readonly ?array $conflictItems = null,
    ) {}
}
