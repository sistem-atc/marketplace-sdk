<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Report;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesCamelCaseKeys;

/**
 * Report do SP-API (GET /reports/2021-06-30/reports/{id}). CAMELCASE (≠ Orders/
 * Pricing que são PascalCase). `processingStatus`: IN_QUEUE|IN_PROGRESS|DONE|
 * CANCELLED|FATAL; quando DONE vem `reportDocumentId` pra baixar o documento.
 *
 * @property list<string>|null $marketplaceIds
 */
final class Report implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $reportId = null,
        public readonly ?string $reportType = null,
        public readonly ?string $processingStatus = null,
        public readonly ?string $reportDocumentId = null,
        public readonly ?string $reportScheduleId = null,
        public readonly ?array $marketplaceIds = null,
        public readonly ?string $dataStartTime = null,
        public readonly ?string $dataEndTime = null,
        public readonly ?string $createdTime = null,
        public readonly ?string $processingStartTime = null,
        public readonly ?string $processingEndTime = null,
    ) {}
}
