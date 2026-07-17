<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * NFe da entrega (`deliveries[].invoices[]`). `key` é a chave SEFAZ de 44
 * dígitos — o que o pipeline fiscal consome.
 */
final class DeliveryInvoice implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $key = null,
        public readonly ?string $issuedAt = null,
        public readonly ?InvoiceStatus $status = null,
    ) {}
}
