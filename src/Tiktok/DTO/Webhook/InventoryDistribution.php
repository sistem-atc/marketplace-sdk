<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.inventory_distribution` do webhook TYPE 27 — como o estoque fisico
 * esta' repartido no momento do alerta.
 *
 * Identidade da doc:
 *   total = available + creator_reserved + campaign_reserved + committed
 *
 * Atencao: e' um vocabulario DIFERENTE do snapshot do topico 68
 * (`total_available_quantity`, `in_shop_quantity`, ...). Nao dao' pra somar um
 * com o outro sem traduzir.
 */
final class InventoryDistribution implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $totalQuantity = null,
        public readonly ?int $availableQuantity = null,
        // Reservado pra creators (colaboracao de afiliado).
        public readonly ?int $creatorReservedQuantity = null,
        // Reservado pra campanha de marketing.
        public readonly ?int $campaignReservedQuantity = null,
        // Ja' vendido e ainda nao enviado.
        public readonly ?int $committedQuantity = null,
    ) {}
}
