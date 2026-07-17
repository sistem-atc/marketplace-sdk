<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Situação da NFe da entrega (`deliveries[].invoices[].status`). */
final class InvoiceStatus implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $description = null,
        public readonly ?string $statusDate = null,
    ) {}
}
