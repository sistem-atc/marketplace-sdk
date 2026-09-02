<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Point Payment Intent Event resource. Represents a single event entry in the payment intent
 * history for a Point device. Each event records the payment intent ID, its status at that point
 * in time, and when the event occurred.
 */
final class Events implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Payment intent ID. */
        public readonly ?string $paymentIntentId = null,

        /** Status. */
        public readonly ?string $status = null,

        /** Created on. */
        public readonly ?string $createdOn = null,
    ) {}
}
