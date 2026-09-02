<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Pickup;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;

/**
 * Envios Coletas (cross_docking) e Places (xd_drop_off) + pontos facultativos.
 *
 * Regra geral: vendedor com Multi Origem (tag `warehouse_management` em /users)
 * usa as variantes por NODE_ID (ex.: MXP20157465171); os demais, por USER_ID.
 * logistic_type aceito: `cross_docking` | `xd_drop_off`. service_type do
 * processing_time_tool: `carrier_pickup` (coleta) e afins.
 */
class PickupMethods extends BaseMethods
{
    public const LOGISTIC_CROSS_DOCKING = 'cross_docking';
    public const LOGISTIC_XD_DROP_OFF = 'xd_drop_off';
    public const SERVICE_CARRIER_PICKUP = 'carrier_pickup';

    /**
     * Capacidade diaria de envio por USER_ID
     * (GET /users/{user}/capacity_middleend/{logistic_type}): {peak_season_mode,
     * capacities[{day, capacity_min, capacity_max, capacity{value, maximum, source}, ...}]}.
     *
     * @return array<string,mixed>
     */
    public function userCapacity(int|string $userId, string $logisticType = self::LOGISTIC_CROSS_DOCKING): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/capacity_middleend/".rawurlencode($logisticType));
    }

    /**
     * Atualiza capacidade por USER_ID (PUT /users/{user}/capacity_middleend/{logistic_type}).
     * Body {capacities: [{day, capacity{value, maximum}}]}. `value` nao pode
     * passar de capacity_max (400) nem ficar abaixo de capacity_min.
     *
     * @param  array<int, array<string,mixed>>  $capacities
     * @return array<string,mixed>
     */
    public function updateUserCapacity(int|string $userId, array $capacities, string $logisticType = self::LOGISTIC_CROSS_DOCKING): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            "/users/{$userId}/capacity_middleend/".rawurlencode($logisticType),
            body: ['capacities' => $capacities],
        );
    }

    /**
     * Capacidade por NODE_ID (GET /nodes/{node}/capacity_middleend) — Multi Origem.
     * Mesmo formato de userCapacity().
     *
     * @return array<string,mixed>
     */
    public function nodeCapacity(string $nodeId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/nodes/'.rawurlencode($nodeId).'/capacity_middleend');
    }

    /**
     * Atualiza capacidade por NODE_ID (PUT /nodes/{node}/capacity_middleend).
     * Mesmo body de updateUserCapacity().
     *
     * @param  array<int, array<string,mixed>>  $capacities
     * @return array<string,mixed>
     */
    public function updateNodeCapacity(string $nodeId, array $capacities): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            '/nodes/'.rawurlencode($nodeId).'/capacity_middleend',
            body: ['capacities' => $capacities],
        );
    }

    /**
     * Tempo de preparacao por USER_ID
     * (GET /users/{user}/service/{service_type}/processing_time_tool): por dia da
     * semana {enabled, current_processing_time, available_options[{processing_time "HH:MM", selected, ...}]}.
     * Exige header `X-Version: v3`.
     *
     * @return array<string,mixed>
     */
    public function userProcessingTime(int|string $userId, string $serviceType = self::SERVICE_CARRIER_PICKUP): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/users/{$userId}/service/".rawurlencode($serviceType).'/processing_time_tool',
            headers: ['X-Version' => 'v3'],
        );
    }

    /**
     * Atualiza tempo de preparacao por USER_ID (PUT .../processing_time_tool, `X-Version: v3`).
     * Body {processing_times: {monday: {processing_time: "01:00"}, ...}} no formato
     * devolvido pelo GET. Vazio = padrao da logistica (01:00 cross_docking,
     * 01:30 xd_drop_off). Dia com enabled=false e ignorado. O dia corrente so
     * muda na semana seguinte.
     *
     * @param  array<string, array<string,mixed>>  $processingTimes
     * @return array<string,mixed>
     */
    public function updateUserProcessingTime(int|string $userId, array $processingTimes, string $serviceType = self::SERVICE_CARRIER_PICKUP): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            "/users/{$userId}/service/".rawurlencode($serviceType).'/processing_time_tool',
            body: ['processing_times' => $processingTimes],
            headers: ['X-Version' => 'v3'],
        );
    }

    /**
     * Tempo de preparacao por NODE_ID (GET /nodes/{node}/service/{service_type}/processing_time_tool).
     *
     * @return array<string,mixed>
     */
    public function nodeProcessingTime(string $nodeId, string $serviceType = self::SERVICE_CARRIER_PICKUP): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/nodes/'.rawurlencode($nodeId).'/service/'.rawurlencode($serviceType).'/processing_time_tool',
        );
    }

    /**
     * Atualiza tempo de preparacao por NODE_ID (PUT .../processing_time_tool, `X-Version: v3`).
     * Mesmo body de updateUserProcessingTime(). Resposta {message}.
     *
     * @param  array<string, array<string,mixed>>  $processingTimes
     * @return array<string,mixed>
     */
    public function updateNodeProcessingTime(string $nodeId, array $processingTimes, string $serviceType = self::SERVICE_CARRIER_PICKUP): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            '/nodes/'.rawurlencode($nodeId).'/service/'.rawurlencode($serviceType).'/processing_time_tool',
            body: ['processing_times' => $processingTimes],
            headers: ['X-Version' => 'v3'],
        );
    }

    /**
     * Agenda de coletas/despachos por USER_ID
     * (GET /users/{user}/shipping/schedule/{logistic_type}): {seller_id, schedule{monday{work, detail[{from, to, cutoff, carrier, vehicle, milkrun_same_day}]}, ...}}.
     *
     * @return array<string,mixed>
     */
    public function userSchedule(int|string $userId, string $logisticType = self::LOGISTIC_CROSS_DOCKING): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/shipping/schedule/".rawurlencode($logisticType));
    }

    /**
     * Agenda por NODE_ID (GET /nodes/{node}/schedule/{logistic_type}) — Multi Origem.
     *
     * @return array<string,mixed>
     */
    public function nodeSchedule(string $nodeId, string $logisticType = self::LOGISTIC_XD_DROP_OFF): array
    {
        return $this->makeRequest(HttpMethod::GET, '/nodes/'.rawurlencode($nodeId).'/schedule/'.rawurlencode($logisticType));
    }

    /**
     * Pontos facultativos disponiveis ao vendedor
     * (GET /shipping/seller/{seller}/working_day_middleend): {dates[{date, description, enabled, checked, closed, finalized}]}.
     * checked=true = vendedor NAO opera nesse dia.
     *
     * @return array<string,mixed>
     */
    public function workingDays(int|string $sellerId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/shipping/seller/{$sellerId}/working_day_middleend");
    }

    /**
     * Marca/desmarca pontos facultativos (PUT /shipping/seller/{seller}/working_day_middleend).
     * Body {site_id, dates: [{date, description, checked}]}.
     *
     * @param  array<int, array<string,mixed>>  $dates
     * @return array<string,mixed>
     */
    public function updateWorkingDays(int|string $sellerId, string $siteId, array $dates): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            "/shipping/seller/{$sellerId}/working_day_middleend",
            body: ['site_id' => $siteId, 'dates' => $dates],
        );
    }

    /**
     * Pontos facultativos em que o vendedor optou por NAO operar
     * (GET /shipping/seller/{seller}/working_day_middleend/optout[?date=AAAA-MM-DD]).
     * Sem `date` lista todos; com `date` filtra o dia. Vendedor sem configuracao
     * devolve 200 vazio.
     *
     * @return array<string,mixed>
     */
    public function workingDayOptout(int|string $sellerId, ?string $date = null): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/shipping/seller/{$sellerId}/working_day_middleend/optout",
            $date !== null ? ['date' => $date] : [],
        );
    }
}
