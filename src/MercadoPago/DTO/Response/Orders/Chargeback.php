<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a chargeback dispute within an order transaction. A chargeback occurs when a buyer
 * disputes a charge with their card issuer. This resource tracks the dispute lifecycle including
 * its status and related references.
 */
final class Chargeback implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier of the chargeback assigned by MercadoPago. */
        public readonly ?string $id = null,

        /** Identifier of the original payment transaction that was disputed. */
        public readonly ?string $transactionId = null,

        /** Case identifier opened with the card network or issuing bank. */
        public readonly ?string $caseId = null,

        /** Current status of the chargeback dispute (e.g., "opened", "closed"). */
        public readonly ?string $status = null,

        /** External references or evidence associated with this chargeback. */
        public readonly ?array $references = null,
    ) {}
}
