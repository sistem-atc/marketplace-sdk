<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 38 — Strikethrough price expired.
 *
 * Dispara 90 dias apos o envio da comprovacao do "preco de" (preco riscado).
 * Sem reenviar a verificacao, o preco riscado deixa de ser exibido — evento
 * comercial, nao de estoque. Payload minimo: so' produto + SKU, sem timestamp
 * proprio (use o `timestamp` do envelope).
 */
final class StrikethroughPriceExpiredWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        public readonly ?string $skuId = null,
    ) {}
}
