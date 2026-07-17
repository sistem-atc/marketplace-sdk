<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Logística da entrega (`deliveries[].shipping`). O `provider.extras.isFulfillment`
 * marca Magalu Entregas (fulfillment/MLE) — gatilho das 2 NFes.
 */
final class Shipping implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?TimeSpec $deadline = null,
        public readonly ?TimeSpec $handlingTime = null,
        public readonly ?string $deliveredAt = null,
        public readonly ?string $shippedAt = null,
        public readonly ?string $cancelledAt = null,
        public readonly ?Provider $provider = null,
        public readonly ?LogisticNetwork $logisticNetwork = null,
        public readonly ?Recipient $recipient = null,
        public readonly ?PickupDetails $pickupDetails = null,
        public readonly ?Tracking $tracking = null,
        public readonly ?string $trackingUrl = null,
    ) {}
}
