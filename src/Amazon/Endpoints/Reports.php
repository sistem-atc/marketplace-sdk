<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Support\FlattensCsvQuery;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Report\Report;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Report\ReportDocument;

/**
 * Reports API v2021-06-30.
 */
class Reports
{
    use FlattensCsvQuery;

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

    // -------------------------------------------------------------------
    // Report schedules (agendamento recorrente)
    // -------------------------------------------------------------------

    /**
     * Lista os agendamentos de relatório (GET /reports/2021-06-30/schedules)
     * dos tipos informados (obrigatório, csv). Retorna `reportSchedules[]`.
     * Rate limit: 0.0222 req/s + burst 10.
     *
     * @param  list<string>  $reportTypes
     * @return array<string, mixed>
     */
    public function getReportSchedules(array $reportTypes): array
    {
        return $this->client->get('/reports/2021-06-30/schedules', $this->csv(['reportTypes' => $reportTypes]));
    }

    /**
     * Cria um agendamento recorrente (POST /reports/2021-06-30/schedules).
     * `period` em ISO8601 duration (PT5M, PT15M, PT30M, PT1H, PT2H, PT4H,
     * PT8H, PT12H, P1D, P2D, P3D, PT84H, P7D, P14D, P15D, P18D, P30D).
     * Só um schedule por (reportType, marketplaceIds): o novo substitui o
     * anterior. Extras: reportOptions, nextReportCreationTime. Retorna
     * `reportScheduleId`. Rate limit: 0.0222 req/s + burst 10.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function createReportSchedule(string $reportType, array $marketplaceIds, string $period, array $options = []): array
    {
        return $this->client->post('/reports/2021-06-30/schedules', [
            'reportType' => $reportType,
            'marketplaceIds' => $marketplaceIds,
            'period' => $period,
        ] + $options);
    }

    /**
     * Um agendamento (GET /reports/2021-06-30/schedules/{reportScheduleId}).
     * Rate limit: 0.0222 req/s + burst 10.
     *
     * @return array<string, mixed>
     */
    public function getReportSchedule(string $reportScheduleId): array
    {
        return $this->client->get('/reports/2021-06-30/schedules/'.rawurlencode($reportScheduleId));
    }

    /**
     * Cancela um agendamento (DELETE /reports/2021-06-30/schedules/{reportScheduleId}).
     * Resposta 200 vazia. Rate limit: 0.0222 req/s + burst 10.
     *
     * @return array<string, mixed>
     */
    public function cancelReportSchedule(string $reportScheduleId): array
    {
        return $this->client->delete('/reports/2021-06-30/schedules/'.rawurlencode($reportScheduleId));
    }
}
