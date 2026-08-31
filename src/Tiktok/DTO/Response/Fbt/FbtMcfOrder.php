<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pedido MCF — estoque parado no armazem do TikTok atendendo venda de OUTRO
 * canal (site proprio, Shopify, outro marketplace).
 *
 * `externalOrderId` e' o id do pedido no NOSSO OMS: e' a unica ponte entre a
 * saida fisica do armazem FBT e o pedido/nota que o ERP emitiu. Guardar isso e'
 * o que evita saida sem lastro no inventario de terceiro.
 *
 * `consignOrders` so' aparece depois do despacho — no retorno do CREATE vem
 * apenas id/external/create_time.
 *
 * `createTime` e' epoch em SEGUNDOS.
 *
 * @property list<FbtConsignOrder>|null $consignOrders
 */
final class FbtMcfOrder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $externalOrderId = null,
        public readonly ?string $mcfOrderId = null,
        #[ArrayOf(FbtConsignOrder::class)]
        public readonly ?array $consignOrders = null,
        public readonly ?int $createTime = null,
    ) {}
}
