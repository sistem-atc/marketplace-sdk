<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /fbt/202408/inventory/search`.
 *
 * Sem filtro, devolve TODO goods com saldo diferente de zero em TODO armazem
 * assinado. A API aceita `goods_ids` OU `sku_ids`, nunca os dois juntos.
 *
 * @property list<FbtInventoryItem>|null $inventory
 */
final class FbtInventorySearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
        #[ArrayOf(FbtInventoryItem::class)]
        public readonly ?array $inventory = null,
    ) {}
}
