<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Analytics;

use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class AnalyticsMethods extends BaseMethods
{
    /**
     * Lista relatorios configurados na loja.
     */
    public function listReports(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/reports', $params);
    }

    /**
     * Recupera detalhes de um relatorio especifico.
     */
    public function getReport(int|string $reportId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/reports/{$reportId}");
    }
}
