<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item da nota (`items[]`).
 *
 * @property list<Payment> $payments
 */
final class InvoiceItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<Payment> $payments */
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $invoiceId = null,
        public readonly ?string $sellerId = null,
        public readonly ?string $externalOrderId = null,
        public readonly ?string $externalProductId = null,
        public readonly ?ItemAttributes $attributes = null,
        public readonly ?string $productName = null,
        public readonly ?float $quantity = null,
        public readonly ?float $totalAmount = null,
        public readonly ?float $shippingBuyerCost = null,
        public readonly ?DiscountAmount $discountAmount = null,
        public readonly ?ItemFiscalData $fiscalData = null,
        #[ArrayOf(Payment::class)]
        public readonly array $payments = [],
        public readonly ?string $additionalInfo = null,
        public readonly ?float $otherAmount = null,
    ) {}
}
