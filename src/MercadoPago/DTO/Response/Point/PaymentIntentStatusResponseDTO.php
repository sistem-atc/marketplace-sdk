<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Payment Intent Status resource (Point integration). Represents the current processing status of
 * a payment intent on a Point device, including when the status was recorded.
 */
final class PaymentIntentStatusResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Status of payment intent. */
        public readonly ?string $status = null,

        /** Date created. */
        public readonly ?string $createdOn = null,
    ) {}
}
