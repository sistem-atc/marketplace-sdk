<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Remessa fisica gerada por um pedido MCF (`consign_orders[]`).
 *
 * UM pedido MCF pode virar VARIAS consign orders (o armazem quebra por
 * disponibilidade/origem). Cada uma tem rastreio proprio — e, no ERP, cada
 * remessa e' um evento de estoque separado.
 *
 * `isPlatformClosed` desambigua QUEM cancelou: true = a plataforma FBT cancelou
 * (falta de estoque, item bloqueado); false = o cancelamento veio do seu OMS.
 * Sem esse campo os dois casos chegam identicos como CLOSED.
 *
 * `trackingNumber`, `carrier` e `shippingProvider` sao NULL ate' a
 * transportadora receber o volume.
 *
 * @property list<FbtMcfGoodsItem>|null $goods
 */
final class FbtConsignOrder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        #[ArrayOf(FbtMcfGoodsItem::class)]
        public readonly ?array $goods = null,
        public readonly ?string $trackingNumber = null,
        public readonly ?string $carrier = null,
        // PENDING | HANDLE_BY_WAREHOUSE | SHIPPED | HANDOVER | CLOSED | LOST |
        // DAMAGE | COMPLETED | ABNORMAL. O cancel devolve "cancelled" em
        // minuscula no exemplo oficial — nao compare com case-sensitive.
        public readonly ?string $status = null,
        public readonly ?bool $isPlatformClosed = null,
        // Motivo do travamento quando status e' ABNORMAL/LOST etc.
        public readonly ?string $issue = null,
        public readonly ?FbtShippingProvider $shippingProvider = null,
    ) {}
}
