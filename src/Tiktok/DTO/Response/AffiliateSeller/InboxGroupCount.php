<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Contagem de conversas por aba da caixa de entrada.
 */
final class InboxGroupCount implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // UNREAD | UNREPLIED | STARRED | ARCHIVED
        public readonly ?string $groupType = null,
        public readonly ?int $count = null,
    ) {}
}
