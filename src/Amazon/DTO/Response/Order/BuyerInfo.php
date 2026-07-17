<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Comprador (`order.BuyerInfo`). Na SP-API o nome/e-mail vêm mascarados sem
 * RDT; `BuyerCounty` (BR) e o CPF (via BuyerTaxInfo) costumam vir.
 */
final class BuyerInfo implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $buyerName = null,
        public readonly ?string $buyerEmail = null,
        public readonly ?string $buyerCounty = null,
        public readonly ?TaxInfo $buyerTaxInfo = null,
        public readonly ?string $purchaseOrderNumber = null,
    ) {}
}
