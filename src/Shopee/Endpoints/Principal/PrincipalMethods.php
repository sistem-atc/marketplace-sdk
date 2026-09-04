<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Principal;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Brand Portal / Principal (api_type Principal) — performance agregada de
 * vendas, afiliados, livestream e video das lojas de um "principal" (marca).
 *
 * Autenticacao: partner_id + path + timestamp + access_token + principal_id
 * (settings['principal_id']). O access_token e' do PRINCIPAL (autorizacao
 * propria no Brand Portal; token de loja nao serve) e o refresh precisa mandar
 * principal_id no body de /auth/access_token/get. Permissao de app: "Brand
 * Portal Service".
 *
 * Todos sao POST com body JSON. Comum a todos: start_date/end_date
 * (YYYY-MM-DD), timezone ("GMT+7" | "GMT+8" | "GMT-3") e granularity
 * (customize | day | week | month | quarter | year — a granularidade valida a
 * janela: day exige start=end, week exige seg-dom etc.). Retorno bruto `array`.
 */
class PrincipalMethods extends BaseMethods
{
    protected string $authEntity = 'principal_id';

    private const BASE = '/api/v2/principal/';

    /** @param array<string,mixed> $extra */
    private function report(string $name, string $startDate, string $endDate, string $timezone, string $granularity, array $extra = []): array
    {
        $body = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'timezone' => $timezone,
            'granularity' => $granularity,
        ];

        foreach ($extra as $key => $value) {
            if ($value === null || $value === []) {
                continue;
            }
            $body[$key] = is_array($value) && array_is_list($value) ? array_values($value) : $value;
        }

        return $this->makeRequest(HttpMethod::POST, self::BASE.$name, [], $body);
    }

    // -------------------------------------------------------------- vendas

    /** Vendas por LOJA. shop_list vazio = todas as lojas do principal. @param array<int,array{shop_id:int,currency?:string}> $shopList */
    public function getShopSalesPerformanceDetail(string $startDate, string $endDate, string $timezone = 'GMT-3', string $granularity = 'day', array $shopList = []): array
    {
        return $this->report('get_shop_sales_performance_detail', $startDate, $endDate, $timezone, $granularity, ['shop_list' => $shopList]);
    }

    /** Vendas agregadas do PRINCIPAL por regiao. region_list vazio = todas. @param array<int,array{region:string,currency?:string}> $regionList */
    public function getPrincipalSalesPerformanceDetail(string $startDate, string $endDate, string $timezone = 'GMT-3', string $granularity = 'day', array $regionList = []): array
    {
        return $this->report('get_principal_sales_performance_detail', $startDate, $endDate, $timezone, $granularity, ['region_list' => $regionList]);
    }

    // ------------------------------------------------------------ afiliados

    /** @param array<int,array{shop_id:int,currency?:string}> $shopList */
    public function getShopAffiliatePerformance(string $startDate, string $endDate, string $timezone = 'GMT-3', string $granularity = 'day', array $shopList = []): array
    {
        return $this->report('get_shop_affiliate_performance', $startDate, $endDate, $timezone, $granularity, ['shop_list' => $shopList]);
    }

    /** @param array<int,array{region:string,currency?:string}> $regionList */
    public function getPrincipalAffiliatePerformance(string $startDate, string $endDate, string $timezone = 'GMT-3', string $granularity = 'day', array $regionList = []): array
    {
        return $this->report('get_principal_affiliate_performance', $startDate, $endDate, $timezone, $granularity, ['region_list' => $regionList]);
    }

    /**
     * Performance por CONTEUDO de afiliado. content_list vazio = todos os
     * conteudos elegiveis, paginados por page_size [1,200] + cursor (offset
     * zero-based) — page_size/cursor so' valem SEM content_list.
     *
     * @param array<int,array{shop_id:int,content_ids:int[],currency?:string}> $contentList
     */
    public function getContentAffiliatePerformance(string $startDate, string $endDate, string $timezone = 'GMT-3', string $granularity = 'day', array $contentList = [], ?int $pageSize = null, ?int $cursor = null): array
    {
        return $this->report('get_content_affiliate_performance', $startDate, $endDate, $timezone, $granularity, [
            'content_list' => $contentList,
            'page_size' => $pageSize,
            'cursor' => $cursor,
        ]);
    }

    // ----------------------------------------------------------- livestream

    /** @param array<int,array{shop_id:int,currency?:string}> $shopList */
    public function getShopLivestreamPerformance(string $startDate, string $endDate, string $timezone = 'GMT-3', string $granularity = 'day', array $shopList = []): array
    {
        return $this->report('get_shop_livestream_performance', $startDate, $endDate, $timezone, $granularity, ['shop_list' => $shopList]);
    }

    /** @param array<int,array{region:string,currency?:string}> $regionList */
    public function getPrincipalLivestreamPerformance(string $startDate, string $endDate, string $timezone = 'GMT-3', string $granularity = 'day', array $regionList = []): array
    {
        return $this->report('get_principal_livestream_performance', $startDate, $endDate, $timezone, $granularity, ['region_list' => $regionList]);
    }

    /**
     * Performance por SESSAO de live. session_list vazio = todas, paginadas
     * por page_size [1,200] + cursor (so' valem sem session_list).
     *
     * @param array<int,array{shop_id:int,session_ids:int[],currency?:string}> $sessionList
     */
    public function getSessionLivestreamPerformance(string $startDate, string $endDate, string $timezone = 'GMT-3', string $granularity = 'day', array $sessionList = [], ?int $pageSize = null, ?int $cursor = null): array
    {
        return $this->report('get_session_livestream_performance', $startDate, $endDate, $timezone, $granularity, [
            'session_list' => $sessionList,
            'page_size' => $pageSize,
            'cursor' => $cursor,
        ]);
    }

    // ---------------------------------------------------------------- video

    /** shop_list: no maximo 50 lojas. @param array<int,array{shop_id:int,currency?:string}> $shopList */
    public function getShopVideoPerformance(string $startDate, string $endDate, string $timezone = 'GMT-3', string $granularity = 'day', array $shopList = []): array
    {
        return $this->report('get_shop_video_performance', $startDate, $endDate, $timezone, $granularity, ['shop_list' => $shopList]);
    }

    /** @param array<int,array{region:string,currency?:string}> $regionList */
    public function getPrincipalVideoPerformance(string $startDate, string $endDate, string $timezone = 'GMT-3', string $granularity = 'day', array $regionList = []): array
    {
        return $this->report('get_principal_video_performance', $startDate, $endDate, $timezone, $granularity, ['region_list' => $regionList]);
    }

    /**
     * Performance por CLIPE de video. video_list vazio = todos, paginados por
     * page_size [1,200] + cursor (so' valem sem video_list). Ate' 100 objetos
     * x 100 video_ids.
     *
     * @param array<int,array{shop_id:int,video_ids:int[],currency?:string}> $videoList
     */
    public function getClipVideoPerformance(string $startDate, string $endDate, string $timezone = 'GMT-3', string $granularity = 'day', array $videoList = [], ?int $pageSize = null, ?int $cursor = null): array
    {
        return $this->report('get_clip_video_performance', $startDate, $endDate, $timezone, $granularity, [
            'video_list' => $videoList,
            'page_size' => $pageSize,
            'cursor' => $cursor,
        ]);
    }
}
