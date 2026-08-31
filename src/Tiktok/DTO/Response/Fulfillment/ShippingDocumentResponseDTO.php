<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado de GET /fulfillment/202309/packages/{id}/shipping_documents.
 *
 * `docUrl` VENCE EM 24H (doc). Baixe o PDF/PNG na hora e guarde o binario —
 * persistir a URL da' link morto no dia seguinte.
 */
final class ShippingDocumentResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $docUrl = null,
        public readonly ?string $trackingNumber = null,
    ) {}
}
