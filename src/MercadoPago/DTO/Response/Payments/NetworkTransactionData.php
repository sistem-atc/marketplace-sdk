<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents card network transaction identifiers in the MercadoPago API. Contains the network
 * transaction ID assigned by the card scheme (Visa, Mastercard, etc.) which is required for
 * Merchant-Initiated Transactions (MIT) and recurring payment flows. Nested within ForwardData.
 */
final class NetworkTransactionData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique transaction identifier assigned by the card network for traceability. */
        public readonly ?string $networkTransactionId = null,
    ) {}
}
