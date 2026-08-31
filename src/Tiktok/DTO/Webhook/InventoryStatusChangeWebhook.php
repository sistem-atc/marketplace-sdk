<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 27 — Inventory status change.
 *
 * NAO e' o feed de movimentacao de estoque: e' um ALERTA. So' dispara quando o
 * SKU cai pra LOW_STOCK/OUT_OF_STOCK ou quando o TikTok PREVE ruptura em X
 * dias. Venda normal que apenas reduz saldo nao gera evento aqui — isso e'
 * papel do topico 68 (Inventory changed), que e' de altissimo volume.
 */
final class InventoryStatusChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        public readonly ?string $skuId = null,
        public readonly ?InventoryAlertTriggerReason $triggerReason = null,
        // SUFFICIENT_STOCK | LOW_STOCK | OUT_OF_STOCK
        public readonly ?string $currentInventoryStatus = null,
        public readonly ?InventoryDistribution $inventoryDistribution = null,
        public readonly ?int $updateTime = null,
    ) {}
}
