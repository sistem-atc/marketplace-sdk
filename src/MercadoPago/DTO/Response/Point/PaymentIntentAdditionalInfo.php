<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Point Payment Intent Additional Info resource. Contains supplementary metadata for a Point
 * payment intent, such as an external reference for reconciliation and whether to print a receipt
 * on the terminal.
 */
final class PaymentIntentAdditionalInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** External reference of the payment. */
        public readonly ?string $externalReference = null,

        /** Print on terminal flag. */
        public readonly ?bool $printOnTerminal = null,
    ) {}
}
