<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Logistics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item do `result_list` de get_shipping_document_result — status da geração do
 * documento (`status`: READY | PROCESSING | FAILED | EXPIRED). Em falha vêm
 * `failError`/`failMessage`.
 */
final class ShippingDocumentResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderSn = null,
        public readonly ?string $packageNumber = null,
        public readonly ?string $status = null,
        public readonly ?string $shippingDocumentType = null,
        public readonly ?string $failError = null,
        public readonly ?string $failMessage = null,
    ) {}
}
