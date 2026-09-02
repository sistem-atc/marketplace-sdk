<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Payment Intent Cancel resource (Point integration). Represents the result of cancelling a
 * payment intent on a Point device. Contains the ID of the cancelled payment intent.
 */
final class PaymentIntentCancelResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** ID of the payment intent. */
        public readonly ?string $id = null,
    ) {}
}
