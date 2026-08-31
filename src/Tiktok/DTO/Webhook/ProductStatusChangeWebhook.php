<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 5 — Product status change.
 *
 * Dispara quando o resultado da auditoria do anuncio muda. NAO fala de estoque
 * nem de preco: e' o ciclo de vida do ANUNCIO na plataforma.
 *
 * Quirk: no exemplo oficial `product_id` vem como NUMERO (576486316948490000),
 * nao string — e' um snowflake que nao cabe com precisao em float de JSON. Por
 * isso tipamos string: o AutoHydrate converte na entrada e o id sobrevive.
 */
final class ProductStatusChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        // PRODUCT_FIRST_PASS_REVIEW | PRODUCT_STATUS_CHANGED | PRODUCT_AUDIT_FAILURE
        public readonly ?string $status = null,
        // Motivo da reprovacao/congelamento; vem "" quando aprovado.
        public readonly ?string $suspendedReason = null,
        // Epoch em SEGUNDOS.
        public readonly ?int $updateTime = null,
    ) {}
}
