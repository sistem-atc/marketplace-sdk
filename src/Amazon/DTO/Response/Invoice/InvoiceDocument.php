<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesCamelCaseKeys;

/**
 * Metadados do documento de NFs — `invoicesDocumentUrl` é o ZIP pré-assinado
 * (atencao: a URL S3 expira em ~10s, entao baixe imediatamente).
 */
final class InvoiceDocument implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $invoicesDocumentUrl = null,
        public readonly ?string $invoicesDocumentId = null,
    ) {}
}
