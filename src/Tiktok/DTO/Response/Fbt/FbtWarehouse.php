<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Armazem da rede Fulfilled by TikTok (`data.warehouses[]`).
 *
 * DOIS IDs DIFERENTES, NAO CONFUNDIR:
 *   - `fbtWarehouseId`: id no sistema FBT — e' o que entra nas APIs de
 *     inventario/inbound deste grupo.
 *   - `warehouseIds`: LISTA de ids do TikTok Shop correlacionados ao mesmo
 *     armazem fisico — e' o que aparece em `warehouse_id` do PEDIDO.
 * Cruzar pedido x estoque FBT exige esse de/para; usar um no lugar do outro
 * devolve vazio sem erro.
 *
 * IMPACTO FISCAL: cada armazem FBT e' um endereco de terceiro. No ERP, mandar
 * mercadoria pra ca' e' remessa pra armazem geral (nota de remessa) e a venda
 * subsequente sai com nota propria — mesmo par de notas do Full/ML e do FBA.
 *
 * @property list<FbtWarehouseAddress>|null $addresses
 * @property list<FbtLogisticsService>|null $logisticsServices
 */
final class FbtWarehouse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $fbtWarehouseId = null,
        /** @var list<string>|null ids do TikTok Shop ligados a este armazem */
        public readonly ?array $warehouseIds = null,
        public readonly ?string $name = null,
        // Unico valor documentado hoje: PLATFORM_WAREHOUSE
        public readonly ?string $type = null,
        public readonly ?bool $subscribed = null,
        #[ArrayOf(FbtWarehouseAddress::class)]
        public readonly ?array $addresses = null,
        #[ArrayOf(FbtLogisticsService::class)]
        public readonly ?array $logisticsServices = null,
    ) {}
}
