<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the response from updating a transaction within a MercadoPago order. Returned by the
 * update transaction endpoint, this resource contains the updated payment method details after
 * modifying an existing transaction.
 */
final class OrderTransactionUpdateResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Updated payment method details for the transaction. */
        public readonly ?PaymentMethod $paymentMethod = null,
    ) {}
}
