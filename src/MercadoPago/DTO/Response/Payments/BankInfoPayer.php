<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the payer's (buyer's) bank account details in a bank transfer payment. Nested within
 * BankInfo to identify the source bank account from which the funds were transferred.
 */
final class BankInfoPayer implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier of the payer in the banking context. */
        public readonly ?string $id = null,

        /** Email address associated with the payer's bank account. */
        public readonly ?string $email = null,

        /** Bank account identifier of the payer. */
        public readonly ?string $accountId = null,

        /** Full display name of the payer's bank account. */
        public readonly ?string $longName = null,

        /** External identifier for the payer's account in third-party systems. */
        public readonly ?string $externalAccountId = null,

        /** Name of the account holder at the payer's bank. */
        public readonly ?string $accountHolderName = null,

        /** Payer's identification document associated with the bank account. */
        public readonly ?array $identification = null,
    ) {}
}
