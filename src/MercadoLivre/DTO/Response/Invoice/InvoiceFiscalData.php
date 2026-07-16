<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Bloco fiscal da NOTA (`fiscal_data` da raiz) — nivel documento, distinto do
 * `items[].fiscal_data`.
 *
 * @property list<FiscalMessage> $messages
 * @property list<FiscalAmount> $fiscalAmounts
 */
final class InvoiceFiscalData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<FiscalMessage>  $messages
     * @param  list<FiscalAmount>  $fiscalAmounts
     */
    public function __construct(
        public readonly ?string $customerType = null,
        public readonly ?string $transactionType = null,
        public readonly ?string $transactionTypeDescription = null,
        #[ArrayOf(FiscalMessage::class)]
        public readonly array $messages = [],
        #[ArrayOf(FiscalAmount::class)]
        public readonly array $fiscalAmounts = [],
        public readonly ?string $stateCalculationType = null,
        public readonly ?string $customerTransaction = null,
    ) {}
}
