<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Rateio do estoque disponivel por canal (`total_available_inventory_distribution`).
 *
 * ARMADILHA de estoque: `totalAvailableQuantity` do SKU JA inclui o que esta
 * comprometido com campanha/criador. Quem for espelhar no ERP deve olhar
 * `inShopInventory->quantity` se quiser so' o que a loja pode vender livremente.
 */
final class InventoryDistribution implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(CampaignInventory::class)]
        public readonly ?array $campaignInventory = null,
        #[ArrayOf(CreatorInventory::class)]
        public readonly ?array $creatorInventory = null,
        public readonly ?InShopInventory $inShopInventory = null,
    ) {}
}
