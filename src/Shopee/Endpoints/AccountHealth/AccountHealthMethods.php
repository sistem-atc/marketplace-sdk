<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\AccountHealth;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Módulo `/api/v2/account_health` — saúde da conta (Account Health do Seller
 * Center): métricas de performance, pontos de penalidade, punições, anúncios
 * problemáticos e pedidos atrasados.
 *
 * Tudo GET, paginação por page_no (1-based) + page_size. Penalidades e punições
 * referem-se ao TRIMESTRE corrente (a Shopee zera os pontos a cada trimestre).
 */
class AccountHealthMethods extends BaseMethods
{
    /**
     * Métricas de performance da loja (Late Shipment Rate, Non-Fulfilment Rate,
     * Preparation Time, Response Rate, Rating…): `overall_performance` +
     * `metric_list[]` com target, valor atual e período.
     *
     * @return array<string, mixed>
     */
    public function getShopPerformance(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/account_health/get_shop_performance');

        return $response['response'] ?? [];
    }

    /**
     * Detalhe da ORIGEM de uma métrica: pedidos afetados / anúncios relevantes /
     * violações. `metricId` = id vindo de `getShopPerformance` (1 = Late Shipment
     * Rate, 3 = Non-Fulfilment Rate, 4 = Preparation Time…). A chave da lista
     * devolvida varia por métrica (nfr_order_list, lsr_order_list,
     * violation_listing_list…).
     *
     * @return array<string, mixed>
     */
    public function getMetricSourceDetail(int $metricId, int $pageNo = 1, int $pageSize = 50): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/account_health/get_metric_source_detail', [
            'metric_id' => $metricId,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Histórico de pontos de penalidade do trimestre corrente. `violationType`
     * opcional filtra (5 = High LSR, 6 = High NFR, 7 = many non-fulfilled…).
     *
     * @return array<string, mixed>  penalty_point_list[], total_count
     */
    public function getPenaltyPointHistory(int $pageNo = 1, int $pageSize = 50, ?int $violationType = null): array
    {
        $query = ['page_no' => $pageNo, 'page_size' => $pageSize];
        if ($violationType !== null) {
            $query['violation_type'] = $violationType;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/account_health/get_penalty_point_history', $query);

        return $response['response'] ?? [];
    }

    /**
     * Punições aplicadas no trimestre (restrição de anúncios, suspensão de
     * campanhas…). `punishmentStatus` obrigatório: 1 = Ongoing, 2 = Ended.
     *
     * @return array<string, mixed>  punishment_list[], total_count
     */
    public function getPunishmentHistory(int $punishmentStatus, int $pageNo = 1, int $pageSize = 50): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/account_health/get_punishment_history', [
            'punishment_status' => $punishmentStatus,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Anúncios com problema (Problematic Listings) — corrigir evita pontos de
     * penalidade.
     *
     * @return array<string, mixed>  listing_list[], total_count
     */
    public function getListingsWithIssues(int $pageNo = 1, int $pageSize = 50): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/account_health/get_listings_with_issues', [
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Pedidos ATRASADOS (Late Orders) — ainda dá para agir antes do cancelamento
     * automático/penalidade.
     *
     * @return array<string, mixed>  late_order_list[], total_count
     */
    public function getLateOrders(int $pageNo = 1, int $pageSize = 50): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/account_health/get_late_orders', [
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);

        return $response['response'] ?? [];
    }
}
