<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents bank account information for bank transfer payments in the MercadoPago API. Contains
 * the bank details of both the payer and collector involved in a bank transfer (e.g. Pix, TED)
 * transaction. Nested within TransactionData.
 */
final class BankInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Bank account details of the payer (buyer). */
        public readonly ?BankInfoPayer $payer = null,

        /** Bank account details of the collector (seller). */
        public readonly ?BankInfoCollector $collector = null,

        /** Whether the payer and collector share the same bank account ownership. */
        public readonly ?string $isSameBankAccountOwner = null,

        /** Identifier of the originating bank in the transfer. */
        public readonly ?string $originBankId = null,

        /** Identifier of the originating digital wallet in the transfer. */
        public readonly ?string $originWalletId = null,
    ) {}
}
