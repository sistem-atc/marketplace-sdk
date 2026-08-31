<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do webhook type 24 — FBT inventory update.
 *
 * Saldo FBT por (goods_id, sku_id) e por armazem. E' SNAPSHOT, nao delta: o
 * payload traz o total corrente, entao aplicar como movimento duplicaria
 * estoque — grave como saldo.
 *
 * @property list<FbtWarehouseInventory>|null $fbtWarehouseInventory
 */
final class FbtInventoryUpdateWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $goodsId = null,
        public readonly ?string $skuId = null,
        #[ArrayOf(FbtWarehouseInventory::class)]
        public readonly ?array $fbtWarehouseInventory = null,
        /** Epoch em SEGUNDOS da acao. */
        public readonly ?int $updateTime = null,
    ) {}
}
