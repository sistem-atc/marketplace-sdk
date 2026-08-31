<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do webhook type 64 — Aftersales Request Status Update.
 *
 * PRIMEIRA revisao do pos-venda: o comprador pediu devolucao e o seller aprova
 * ou recusa. Aprovado, o TikTok abre sozinho um RMA (type 65) e so' depois vem
 * o dinheiro (type 67). Nao ha valor nem SKU aqui — e' so' o cabecalho do
 * pedido de pos-venda; nao mexe em titulo nem em nota.
 */
final class AftersalesRequestStatusUpdateWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $aftersalesRequestId = null,
        /** BUYER | SELLER. */
        public readonly ?string $returnRole = null,
        /** PENDING_REQUEST_REVIEW | REQUEST_REVIEW_COMPLETED. */
        public readonly ?string $aftersalesRequestStatus = null,
        /** Epoch em SEGUNDOS. */
        public readonly ?int $aftersalesRequestCreateTime = null,
        /** Epoch em SEGUNDOS. */
        public readonly ?int $aftersalesRequestUpdateTime = null,
    ) {}
}
