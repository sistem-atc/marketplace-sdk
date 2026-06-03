<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Settlement;

use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class SettlementMethods extends BaseMethods
{
    public function createReleasedMoneyReport(string $beginDate, string $endDate, string $format = 'csv'): array
    {
        return $this->makeRequest(HttpMethod::POST, '/v1/account/release_report', [], [
            'begin_date' => $beginDate . 'T00:00:00Z',
            'end_date' => $endDate . 'T23:59:59Z',
            'file_format' => $format,
        ]);
    }

    public function listReports(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/v1/account/settlement_report/list');
    }

    public function downloadReport(string $fileName): string
    {
        $response = $this->httpClient->timeout(300)->get("/v1/account/settlement_report/{$fileName}");
        return (string) $response->body();
    }
}
