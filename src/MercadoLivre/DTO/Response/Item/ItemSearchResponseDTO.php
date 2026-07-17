<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\Paging;

/**
 * GET /users/{seller}/items/search — devolve só os **IDs** (MLB) dos anúncios,
 * não os anúncios (pra hidratar use multiGet).
 *
 * `results` = list<string> de MLBs. Paginação por `scrollId` quando
 * search_type=scan (o offset padrão bate no teto de 1000).
 *
 * @property array<int, string> $results
 * @property array<int|string, mixed> $orders
 * @property array<int|string, mixed> $availableOrders
 */
final class ItemSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  array<int, string>  $results
     * @param  array<int|string, mixed>  $orders
     * @param  array<int|string, mixed>  $availableOrders
     */
    public function __construct(
        public readonly array $results = [],
        public readonly ?string $scrollId = null,
        public readonly ?int $sellerId = null,
        public readonly ?Paging $paging = null,
        public readonly mixed $query = null,
        public readonly array $orders = [],
        public readonly array $availableOrders = [],
    ) {}
}
