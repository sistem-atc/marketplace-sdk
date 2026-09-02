<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents barcode data for offline payment methods (e.g. boleto) in the MercadoPago API.
 * Nested within TransactionDetails to provide the scannable barcode content for ticket-based
 * payment methods.
 */
final class Barcode implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Raw barcode content string that can be rendered as a scannable barcode. */
        public readonly ?string $content = null,
    ) {}
}
