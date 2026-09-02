<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Payment Intent resource (Point integration). Represents a payment intent created for a
 * MercadoPago Point smart terminal device. A payment intent instructs a Point device to initiate
 * a card-present transaction with the specified amount, description, and payment configuration.
 */
final class PaymentIntentResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Additional info of the payment intent. */
        public readonly ?PaymentIntentAdditionalInfo $additionalInfo = null,

        /** Amount of the payment intent. */
        public readonly ?float $amount = null,

        /** Description of the payment intent. */
        public readonly ?string $description = null,

        /** Device id for the payment intent. */
        public readonly ?string $deviceId = null,

        /** ID of the payment intent. */
        public readonly ?string $id = null,

        /** Payment intent details. */
        public readonly ?PaymentIntentPayment $payment = null,

        /** Payment intent mode. */
        public readonly ?string $paymentMode = null,

        /** State of the payment intent. */
        public readonly ?string $state = null,
    ) {}
}
