<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Miolo de `data` do topico 56 — Sample Application Status Change: a solicitacao
 * de AMOSTRA GRATIS de um criador mudou de status.
 *
 * 💸 Por que este topico importa pro faturamento: amostra gratis vira pedido
 * normal no fluxo de pedidos, e o unico sinal de que aquilo NAO e' venda so'
 * aparece depois. Este webhook e' o aviso ANTECIPADO — ele dispara no ciclo da
 * APLICACAO (PENDING -> AWAITING_SHIPMENT -> SHIPPED -> ...), antes de haver
 * expedicao.
 *
 * ⚠️ LIMITE IMPORTANTE: o payload NAO traz `order_id`. As chaves sao
 * `application_id` + `creator.creator_open_id` + `product.id`/`product.sku_id`.
 * Pra ligar a amostra ao pedido e' preciso um segundo passo (Get Creator Sample
 * Application Detail / Seller Search Sample Applications) ou casar por
 * criador+SKU. Nao existe atalho no webhook.
 *
 * Estados terminais de CANCELAMENTO (nao geram amostra): REJECT_CANCELLED,
 * OVERDUE_CANCELLED, UNFULFILL_CANCELLED, DEL_OPEN_COLLAB,
 * SELLER_NOT_SHIP_CANCELLED, WITHDRAW_CANCELLED, UNFULFILLABLE_CANCELLED,
 * OPS_CANCELLED, OPS_FAILED.
 */
final class SampleApplicationStatusChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** ID da solicitacao de amostra. NAO e' order_id. */
        public readonly ?string $applicationId = null,
        /**
         * Status NOVO. PENDING | AWAITING_SHIPMENT | SHIPPED | CONTENT_PENDING |
         * REJECT_CANCELLED | OVERDUE_CANCELLED | UNFULFILL_CANCELLED |
         * DEL_OPEN_COLLAB | SELLER_NOT_SHIP_CANCELLED | WITHDRAW_CANCELLED |
         * UNFULFILLABLE_CANCELLED | OPS_CANCELLED | OPS_FAILED | OPS_COMPLETED |
         * COMPLETED
         */
        public readonly ?string $newStatus = null,
        public readonly ?SampleApplicationCreator $creator = null,
        public readonly ?SampleApplicationProduct $product = null,
    ) {}
}
