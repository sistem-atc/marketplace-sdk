<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Dados da NFe do pedido (`invoice_data`) — o que o pipeline fiscal consome.
 *
 * `accessKey` é a chave de 44 dígitos da NFe (casa com
 * `invoice_xmls.prot_ch_nfe`).
 */
final class InvoiceData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $number = null,
        public readonly ?string $seriesNumber = null,
        public readonly ?string $accessKey = null,
        public readonly ?int $issueDate = null,
        public readonly ?float $totalValue = null,
        public readonly ?float $productsTotalValue = null,
        public readonly ?string $taxCode = null,
    ) {}
}
