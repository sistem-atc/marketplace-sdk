<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Insights;

use SistemAtc\Marketplaces\Amazon\Ads\Bases\BaseMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;

/**
 * Insights da Amazon Ads API: Amazon Attribution (anunciantes, publishers,
 * tags e relatório), Brand Metrics (relatório assíncrono) e Audience
 * Insights (audiências sobrepostas). Tudo leva Scope (profileId).
 */
class InsightsMethods extends BaseMethods
{
    // ----------------------------------------------------------------- Amazon Attribution

    /**
     * Anunciantes da conta Amazon Attribution (GET /attribution/advertisers).
     *
     * @return array<string, mixed>  {advertisers: [...]}
     *
     * @throws AmazonAdsRequestException
     */
    public function getAdvertisersByProfile(string $profileId): array
    {
        return $this->request(method: 'GET', path: '/attribution/advertisers', profileId: $profileId);
    }

    /**
     * Publishers disponíveis (GET /attribution/publishers).
     *
     * @return array<string, mixed>  {publishers: [...]}
     *
     * @throws AmazonAdsRequestException
     */
    public function getPublishers(string $profileId): array
    {
        return $this->request(method: 'GET', path: '/attribution/publishers', profileId: $profileId);
    }

    /**
     * Relatório Amazon Attribution (POST /attribution/report). O operationId
     * da spec é enganoso — devolve métricas, não tags. Paginação por `cursorId`.
     *
     * @param  array<string, mixed>  $body  {reportType: PERFORMANCE|PRODUCTS, advertiserIds (csv), startDate, endDate (YYYYMMDD), metrics? (csv), groupBy?, count?, cursorId?}
     * @return array<string, mixed>  {reports: [...], cursorId?, size}
     *
     * @throws AmazonAdsRequestException
     */
    public function getAttributionTagsByCampaign(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/attribution/report',
            profileId: $profileId,
            body: $body,
            contentType: 'application/json',
        );
    }

    /**
     * Tags de atribuição pra publishers COM suporte a macros
     * (GET /attribution/tags/macroTag). Ids vão em csv na query.
     *
     * @param  list<string>  $publisherIds
     * @param  list<string|int>  $advertiserIds  vazio = todos
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getPublisherAttributionTagTemplate(string $profileId, array $publisherIds, array $advertiserIds = []): array
    {
        return $this->attributionTags('/attribution/tags/macroTag', $profileId, $publisherIds, $advertiserIds);
    }

    /**
     * Tags de atribuição pra publishers SEM suporte a macros
     * (GET /attribution/tags/nonMacroTemplateTag). Ids vão em csv na query.
     *
     * @param  list<string>  $publisherIds
     * @param  list<string|int>  $advertiserIds  vazio = todos
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getPublisherMacroAttributionTag(string $profileId, array $publisherIds, array $advertiserIds = []): array
    {
        return $this->attributionTags('/attribution/tags/nonMacroTemplateTag', $profileId, $publisherIds, $advertiserIds);
    }

    /**
     * @param  list<string>  $publisherIds
     * @param  list<string|int>  $advertiserIds
     */
    private function attributionTags(string $path, string $profileId, array $publisherIds, array $advertiserIds): array
    {
        $query = ['publisherIds' => implode(',', $publisherIds)];

        if ($advertiserIds !== []) {
            $query['advertiserIds'] = implode(',', $advertiserIds);
        }

        return $this->request(method: 'GET', path: $path, profileId: $profileId, query: $query);
    }

    // ----------------------------------------------------------------- Brand Metrics

    /**
     * Gera o relatório de Brand Metrics (POST /insights/brandMetrics/report) —
     * ASSÍNCRONO: devolve reportId; acompanhe em getBrandMetricsReport().
     * Media type `application/vnd.insightsbrandmetrics.{v1|v1.1}+json`.
     *
     * @param  array<string, mixed>  $body  {brandName, categoryPath: [...], categoryTreeName?, lookBackPeriod: 1w|1m|3m|6m|1y, reportStartDate?, reportEndDate?, format?: CSV|JSON, metrics?}
     * @return array<string, mixed>  {reportId, status, …}
     *
     * @throws AmazonAdsRequestException
     */
    public function generateBrandMetricsReport(string $profileId, array $body, string $version = 'v1'): array
    {
        return $this->request(
            method: 'POST',
            path: '/insights/brandMetrics/report',
            profileId: $profileId,
            body: $body,
            contentType: "application/vnd.insightsbrandmetrics.{$version}+json",
        );
    }

    /**
     * Status + URL do relatório de Brand Metrics
     * (GET /insights/brandMetrics/report/{reportId}).
     *
     * @return array<string, mixed>  {reportId, status: IN_PROGRESS|SUCCESSFUL|FAILED, location?}
     *
     * @throws AmazonAdsRequestException
     */
    public function getBrandMetricsReport(string $profileId, string $reportId, string $version = 'v1'): array
    {
        return $this->request(
            method: 'GET',
            path: '/insights/brandMetrics/report/'.rawurlencode($reportId),
            profileId: $profileId,
            accept: "application/vnd.insightsbrandmetrics.{$version}+json",
        );
    }

    // ----------------------------------------------------------------- Audience insights

    /**
     * Top audiências que se sobrepõem à informada
     * (GET /insights/audiences/{audienceId}/overlappingAudiences).
     * Accept `application/vnd.insightsaudiencesoverlap.v2+json`; paginação por
     * `nextToken`/`maxResults`; `audienceCategory` (lista) vira csv.
     *
     * @param  string  $adType  DSP|SD
     * @param  array<string, mixed>  $query  advertiserId?, minimumOverlapAffinity?, maximumOverlapAffinity?, audienceCategory?: [...], maxResults?, nextToken?
     * @return array<string, mixed>  {overlappingAudiences: [...], nextToken?}
     *
     * @throws AmazonAdsRequestException
     */
    public function getAudiencesOverlappingAudiences(string $profileId, string $audienceId, string $adType, array $query = []): array
    {
        if (isset($query['audienceCategory']) && is_array($query['audienceCategory'])) {
            $query['audienceCategory'] = implode(',', $query['audienceCategory']);
        }

        return $this->request(
            method: 'GET',
            path: '/insights/audiences/'.rawurlencode($audienceId).'/overlappingAudiences',
            profileId: $profileId,
            query: ['adType' => $adType] + $query,
            accept: 'application/vnd.insightsaudiencesoverlap.v2+json',
        );
    }
}
