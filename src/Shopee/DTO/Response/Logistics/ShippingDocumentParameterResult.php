<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Logistics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item do `result_list` de get_shipping_document_parameter — que tipos de
 * documento de envio dá pra gerar pra este pedido. O consumidor escolhe entre
 * `selectableShippingDocumentType` (fallback pro `suggest`).
 *
 * @property list<string>|null $selectableShippingDocumentType
 */
final class ShippingDocumentParameterResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderSn = null,
        public readonly ?string $packageNumber = null,
        public readonly ?array $selectableShippingDocumentType = null,
        public readonly ?string $suggestShippingDocumentType = null,
    ) {}
}
