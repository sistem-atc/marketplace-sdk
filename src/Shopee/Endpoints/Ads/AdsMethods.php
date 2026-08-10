<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Ads;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Módulo `/api/v2/ads` — publicidade CPC da Shopee.
 *
 * Usa o MESMO shop-token das rotas v2 que já consumimos (escrow, wallet,
 * pedidos): nenhuma permissão nova precisou ser habilitada no Open Platform.
 *
 * ⚠️ As datas aqui são **DD-MM-YYYY** (o resto da API Shopee usa unix timestamp).
 * `2026-08-05` devolve `error_param`.
 *
 * ⚠️ Erro vem com HTTP 200 e o campo `error` preenchido — o BaseMethods da
 * Shopee já trata isso (checa `error` no corpo, não só o status).
 */
class AdsMethods extends BaseMethods
{
    /**
     * Performance DIÁRIA agregada da LOJA inteira. Campo de gasto = `expense`.
     *
     * Limites da API: janela máxima de **1 mês** por chamada
     * (`ads.performance.error_date_range_too_long`) e retenção de **6 meses**
     * (`ads.performance.error_date_too_old`).
     *
     * @param  string  $startDate  DD-MM-YYYY
     * @param  string  $endDate    DD-MM-YYYY
     * @return list<array<string, mixed>>  1 item por dia: date, expense, impression,
     *   clicks, ctr, broad_gmv, broad_roas, direct_order, ...
     */
    public function getAllCpcAdsDailyPerformance(string $startDate, string $endDate): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_all_cpc_ads_daily_performance', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Saldo da conta de ads — cross-check com a "Recarga de Ads" da wallet.
     *
     * @return float
     */
    public function getTotalBalance(): float
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_total_balance');

        return (float) ($response['response']['total_balance'] ?? 0.0);
    }
}
