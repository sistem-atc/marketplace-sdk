<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.trigger_reason` do webhook TYPE 27 — por que o ALERTA de estoque
 * disparou.
 *
 * Os dois campos numericos sao MUTUAMENTE EXCLUSIVOS e a doc e' explicita:
 * `lead_days` so' vem em PREDICTION, `low_stock_threshold` so' vem em REALTIME.
 * O que estiver ausente e' `null` aqui — nao zero.
 */
final class InventoryAlertTriggerReason implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // PREDICTION (previsao de ruptura em X dias) | REALTIME (ja' bateu o piso)
        public readonly ?string $alertType = null,
        // Dias entre `update_time` e a ruptura prevista. So' em PREDICTION.
        public readonly ?int $leadDays = null,
        // Piso de estoque baixo que foi atingido. So' em REALTIME.
        public readonly ?int $lowStockThreshold = null,
    ) {}
}
