<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/order/{v}/orders/search` — paginação por PAGE TOKEN opaco
 * (nem cursor de tempo, nem offset): pagine enquanto `nextPageToken` vier
 * preenchido, repassando-o na chamada seguinte.
 *
 * A janela de busca tem teto de 7 dias no TikTok (ver OrderMethods).
 *
 * ATENÇÃO: o search devolve o pedido COMPLETO (mesma shape do getOrderDetail),
 * não um resumo — diferente de Shopee/ML, onde a listagem só traz o id.
 *
 * @property list<OrderResponseDTO>|null $orders
 */
final class OrderSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(OrderResponseDTO::class)]
        public readonly ?array $orders = null,
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
    ) {}
}
