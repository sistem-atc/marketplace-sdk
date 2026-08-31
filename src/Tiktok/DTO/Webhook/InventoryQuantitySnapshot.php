<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `quantity_snapshot_after_change` do webhook TYPE 68 — saldo do SKU DEPOIS da
 * mudanca. E' snapshot absoluto, nao delta (os deltas vivem em `change_detail`).
 *
 * Identidades da doc:
 *   total_quantity   = total_available_quantity + total_committed_quantity
 *   in_shop_quantity = total_available_quantity - (campaign_locked + creator_locked)
 *
 * `inShopQuantity` e' o que o comprador realmente enxerga como disponivel — e'
 * ele que deve espelhar o nosso saldo de canal, nao `totalAvailableQuantity`.
 */
final class InventoryQuantitySnapshot implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $totalQuantity = null,
        public readonly ?int $totalAvailableQuantity = null,
        // Ja' pedido pelo cliente e ainda nao enviado.
        public readonly ?int $totalCommittedQuantity = null,
        public readonly ?int $inShopQuantity = null,
        public readonly ?int $campaignLockedQuantity = null,
        public readonly ?int $creatorLockedQuantity = null,
    ) {}
}
