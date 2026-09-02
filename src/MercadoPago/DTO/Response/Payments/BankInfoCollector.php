<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the collector's (seller's) bank account details in a bank transfer payment. Nested
 * within BankInfo to identify where the transferred funds are received.
 */
final class BankInfoCollector implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Bank account identifier of the collector. */
        public readonly ?string $accountId = null,

        /** Full display name of the bank account. */
        public readonly ?string $longName = null,

        /** Name of the account holder at the bank. */
        public readonly ?string $accountHolderName = null,

        /** Transfer-specific account identifier used by the banking system. */
        public readonly ?string $transferAccountId = null,
    ) {}
}
