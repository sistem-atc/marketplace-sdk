<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Report;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesCamelCaseKeys;

/**
 * Documento do report (GET /reports/2021-06-30/documents/{id}). `url` é o link
 * S3 pré-assinado (expira ~5min); `compressionAlgorithm` = GZIP quando o corpo
 * vem comprimido.
 */
final class ReportDocument implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $reportDocumentId = null,
        public readonly ?string $url = null,
        public readonly ?string $compressionAlgorithm = null,
    ) {}
}
