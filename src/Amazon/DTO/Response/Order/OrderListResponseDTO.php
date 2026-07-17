<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Resposta de `GET /orders/v0/orders` (o `payload`) — lista + `NextToken`.
 *
 * Paginação por NextToken (opaco). A Amazon ordena por LastUpdateDate ASC,
 * então dá pra usar o maior LastUpdateDate da página como cursor resumível.
 *
 * @property list<OrderResponseDTO>|null $orders
 */
final class OrderListResponseDTO implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(OrderResponseDTO::class)]
        public readonly ?array $orders = null,
        public readonly ?string $nextToken = null,
        public readonly ?string $lastUpdatedBefore = null,
        public readonly ?string $createdBefore = null,
    ) {}
}
