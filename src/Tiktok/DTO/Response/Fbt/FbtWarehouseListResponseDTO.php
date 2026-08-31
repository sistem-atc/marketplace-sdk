<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `GET /fbt/202408/warehouses`.
 *
 * Sem paginacao: devolve todos os armazens FBT visiveis pra loja de uma vez
 * (inclusive os NAO assinados — filtre por `subscribed`).
 *
 * @property list<FbtWarehouse>|null $warehouses
 */
final class FbtWarehouseListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FbtWarehouse::class)]
        public readonly ?array $warehouses = null,
    ) {}
}
