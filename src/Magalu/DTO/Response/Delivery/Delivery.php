<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Delivery;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Entrega (GET /seller/v1/deliveries/{id}). O consumidor usa `orderId` pra
 * amarrar a entrega ao pedido; os demais campos (status/rastreio/datas) ficam
 * disponíveis pra enriquecer expedição. Shape-based (endpoint transiente, sem
 * raw armazenado) — campos extras que a API traga passam intactos no
 * toArray()/roundtrip são cobertos por teste sintético.
 */
final class Delivery implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $orderId = null,
        // O ref do pedido às vezes vem aninhado (`order.id`/`order.code`) em vez
        // de `order_id` — passthrough pra preservar o fallback do consumidor.
        public readonly mixed $order = null,
        public readonly ?string $status = null,
        public readonly ?string $code = null,
        public readonly ?string $carrier = null,
        public readonly ?string $trackingCode = null,
        public readonly ?string $trackingUrl = null,
        public readonly ?string $estimatedDeliveryDate = null,
        public readonly ?string $shippedAt = null,
        public readonly ?string $deliveredAt = null,
    ) {}
}
