<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do webhook type 65 — RMA Status Update.
 *
 * SEGUNDA revisao: o pacote devolvido chegou e o seller decide o reembolso.
 * PENDING_PACKAGE_REVIEW tem SLA de 2 dias uteis — passou, o TikTok decide
 * sozinho. Fiscal: e' aqui que a mercadoria volta fisicamente, entao e' o
 * gatilho natural da NF-e de ENTRADA (devolucao), nao o type 67 (que e' so'
 * dinheiro).
 *
 * Divergencia doc x exemplo nos timestamps: a tabela diz
 * `rma_request_create_time`/`rma_request_update_time`, o exemplo oficial manda
 * `rma_create_time`/`rma_update_time`. Os quatro estao mapeados — quem consome
 * usa o par que vier preenchido.
 */
final class RmaStatusUpdateWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $rmaId = null,
        /** RMA sempre nasce de um aftersales request (type 64). */
        public readonly ?string $aftersalesRequestId = null,
        /** BUYER | SELLER. */
        public readonly ?string $returnRole = null,
        /** RMA_CREATED | PENDING_PACKAGE_REVIEW | PACKAGE_REVIEW_COMPLETED. */
        public readonly ?string $rmaStatus = null,
        /** Grafia do EXEMPLO oficial. Epoch em SEGUNDOS. */
        public readonly ?int $rmaCreateTime = null,
        /** Grafia do EXEMPLO oficial. Epoch em SEGUNDOS. */
        public readonly ?int $rmaUpdateTime = null,
        /** Grafia da TABELA de parametros. Epoch em SEGUNDOS. */
        public readonly ?int $rmaRequestCreateTime = null,
        /** Grafia da TABELA de parametros. Epoch em SEGUNDOS. */
        public readonly ?int $rmaRequestUpdateTime = null,
    ) {}
}
