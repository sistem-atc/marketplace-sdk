<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Bloco fiscal do item (`items[].fiscal_data`).
 *
 * @property list<FiscalMessage> $messages
 * @property list<TaxRule> $rules
 */
final class ItemFiscalData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<FiscalMessage>  $messages
     * @param  list<TaxRule>  $rules
     */
    public function __construct(
        public readonly ?ItemFiscalAttributes $attributes = null,
        #[ArrayOf(FiscalMessage::class)]
        public readonly array $messages = [],
        #[ArrayOf(TaxRule::class)]
        public readonly array $rules = [],
    ) {}
}
