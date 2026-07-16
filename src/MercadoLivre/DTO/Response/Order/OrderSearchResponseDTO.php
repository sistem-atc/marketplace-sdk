<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\Paging;

/**
 * Resposta do GET /orders/search — wrapper paginado cujo `results[]` traz os
 * pedidos na MESMA shape do /orders/{id} (por isso reusa OrderResponseDTO, que
 * e' validado LOSSLESS contra 800 pedidos reais).
 *
 * Consumidores: `->results` (lista de OrderResponseDTO) e `->paging?->total`.
 * Os blocos de metadados da busca (query/sort/filters) ficam como array cru —
 * o ML varia bastante a shape deles e ninguem consome.
 *
 * @property list<OrderResponseDTO> $results
 * @property array<int|string, mixed> $availableSorts
 * @property array<int|string, mixed> $filters
 * @property array<int|string, mixed> $availableFilters
 */
final class OrderSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<OrderResponseDTO>  $results
     * @param  array<int|string, mixed>  $availableSorts
     * @param  array<int|string, mixed>  $filters
     * @param  array<int|string, mixed>  $availableFilters
     */
    public function __construct(
        #[ArrayOf(OrderResponseDTO::class)]
        public readonly array $results = [],
        public readonly ?Paging $paging = null,
        public readonly mixed $query = null,
        public readonly mixed $sort = null,
        public readonly array $availableSorts = [],
        public readonly array $filters = [],
        public readonly array $availableFilters = [],
    ) {}
}
