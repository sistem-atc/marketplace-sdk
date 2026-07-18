<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesCamelCaseKeys;

/**
 * Export de NFs (Invoices API 2024-06-19). `status`: PROCESSING|DONE|CANCELLED|
 * FATAL; quando DONE vem `documentIds` pra baixar os ZIPs.
 *
 * @property list<string>|null $documentIds
 */
final class Export implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $exportId = null,
        public readonly ?string $status = null,
        public readonly ?array $documentIds = null,
        public readonly ?string $errorMessage = null,
    ) {}
}
