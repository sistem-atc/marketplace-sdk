<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/order/{v}/orders/external_order_search`.
 *
 * PESO ESTRATEGICO: e' a volta do caminho aberto por
 * `addExternalOrderReferences`. Dado o NOSSO identificador (o numero da NF-e,
 * por exemplo), devolve o pedido do TikTok que o carrega — sem heuristica de
 * valor, sem janela de tempo, sem ambiguidade quando o mesmo pedido tem duas
 * notas de valores iguais.
 *
 * Sem paginacao: a busca e' por chave (`platform` + `external_order_id`) e o
 * resultado e' o conjunto — normalmente 1 pedido — que carrega aquela
 * referencia. Lista VAZIA nao e' erro: significa "nenhum pedido tem essa
 * referencia gravada" (tipicamente pedido anterior ao inicio da gravacao).
 *
 * @property list<ExternalOrderReference>|null $orders
 */
final class ExternalOrderSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ExternalOrderReference::class)]
        public readonly ?array $orders = null,
    ) {}
}
