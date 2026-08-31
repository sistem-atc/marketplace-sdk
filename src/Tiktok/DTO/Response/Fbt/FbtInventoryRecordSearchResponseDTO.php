<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /fbt/202410/inventory_records/search`.
 *
 * Janela por `create_time_ge`/`create_time_le` em epoch de SEGUNDOS; sem elas,
 * o padrao e' "do inicio ate' agora" — o que num catalogo grande vira
 * paginacao infinita. Sempre limite a janela.
 *
 * @property list<FbtInventoryRecord>|null $inventoryRecords
 */
final class FbtInventoryRecordSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FbtInventoryRecord::class)]
        public readonly ?array $inventoryRecords = null,
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
    ) {}
}
