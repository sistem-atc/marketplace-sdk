<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\SubMerchant;

/**
 * Represents data forwarded to payment acquirers/processors in the MercadoPago API. Used in
 * payment facilitator (PayFac) and gateway integrations to send sub-merchant details and network
 * transaction references to the acquirer. Nested within Payment.
 */
final class ForwardData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Sub-merchant identification and address data for PayFac flows. */
        public readonly ?SubMerchant $subMerchant = null,

        /** Network transaction identifiers for recurring/installment card payments. */
        public readonly ?NetworkTransactionData $networkTransactionData = null,
    ) {}
}
