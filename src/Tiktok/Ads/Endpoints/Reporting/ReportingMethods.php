<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Ads\Endpoints\Reporting;

use SistemAtc\Marketplaces\Tiktok\Ads\Bases\BaseMethods;

/**
 * Relatórios de gasto da Marketing API — GMV Max e auction.
 *
 * As linhas voltam cruas no formato da API:
 * `{dimensions: {stat_time_day: "YYYY-MM-DD[ 00:00:00]"}, metrics: {...}}`
 * — métricas chegam como STRING ("123.45"); conversão é do consumidor.
 */
class ReportingMethods extends BaseMethods
{
    /**
     * Gasto diário das campanhas GMV MAX (criadas no Seller Center).
     * `store_ids` = lojas TikTok Shop do advertiser. Métrica de gasto: `cost`.
     *
     * @param  list<string>  $storeIds
     * @param  string  $startDate  YYYY-MM-DD
     * @param  string  $endDate  YYYY-MM-DD
     * @return list<array<string, mixed>>
     */
    public function gmvMaxDaily(string $advertiserId, array $storeIds, string $startDate, string $endDate): array
    {
        return $this->paginatedList('/open_api/v1.3/gmv_max/report/get/', [
            'advertiser_id' => $advertiserId,
            'store_ids' => json_encode($storeIds),
            'dimensions' => json_encode(['stat_time_day']),
            'metrics' => json_encode(['cost']),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    /**
     * Gasto diário AUCTION no nível do advertiser (Ads Manager tradicional,
     * relatório integrado). Métrica de gasto: `spend`.
     *
     * @param  string  $startDate  YYYY-MM-DD
     * @param  string  $endDate  YYYY-MM-DD
     * @return list<array<string, mixed>>
     */
    public function auctionDaily(string $advertiserId, string $startDate, string $endDate): array
    {
        return $this->paginatedList('/open_api/v1.3/report/integrated/get/', [
            'advertiser_id' => $advertiserId,
            'report_type' => 'BASIC',
            'data_level' => 'AUCTION_ADVERTISER',
            'dimensions' => json_encode(['stat_time_day']),
            'metrics' => json_encode(['spend']),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }
}
