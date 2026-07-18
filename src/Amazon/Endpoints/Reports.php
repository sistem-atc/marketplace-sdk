<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Report\Report;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Report\ReportDocument;

/**
 * Reports API v2021-06-30.
 */
class Reports
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Solicita a geracao de um relatorio.
     */
    public function createReport(string $reportType, array $marketplaceIds, array $options = []): array
    {
        return $this->client->post('/reports/2021-06-30/reports', array_merge([
            'reportType' => $reportType,
            'marketplaceIds' => $marketplaceIds,
        ], $options));
    }

    /**
     * Consulta o status de um relatorio.
     */
    public function getReport(string $reportId): Report
    {
        return Report::fromArray($this->client->get("/reports/2021-06-30/reports/{$reportId}"));
    }

    /**
     * Cancela um relatorio.
     */
    public function cancelReport(string $reportId): array
    {
        return $this->client->delete("/reports/2021-06-30/reports/{$reportId}");
    }

    /**
     * Obtem o documento do relatorio (URL para download).
     */
    public function getReportDocument(string $reportDocumentId): ReportDocument
    {
        return ReportDocument::fromArray($this->client->get("/reports/2021-06-30/documents/{$reportDocumentId}"));
    }
}
