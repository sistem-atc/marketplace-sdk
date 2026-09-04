<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Dsp;

use SistemAtc\Marketplaces\Amazon\Ads\Bases\BaseMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;

/**
 * Amazon DSP Measurement — estudos de Brand Lift, Audience Research,
 * Creative Testing e Omnichannel Metrics (OCM), mais elegibilidade,
 * surveys e catálogo de vendor products.
 *
 * Escopo: o header `Amazon-Advertising-API-Scope` é o **profileId da conta
 * DSP** (não o advertiserId — este vai na query `advertiserId` ou no body
 * de cada estudo). A spec aceita como alternativa o header
 * `Amazon-Ads-AccountId` (Ads Account ID global); passe em `$adsAccountId`
 * quando a conta for desse tipo — os dois podem coexistir.
 *
 * Media types por família (versão mais recente por padrão, sobrescrevível
 * via `$mediaType`/`$accept`):
 *  - estudos/surveys: `application/vnd.studymanagement.v1.3+json` (BRAND_LIFT,
 *    OCM, genéricos) ou `v1.2` (AUDIENCE_RESEARCH, CREATIVE_TESTING);
 *  - elegibilidade: `application/vnd.measurementeligibility.v1.x+json`;
 *  - resultados: `application/vnd.measurementresult.v1.x+json`;
 *  - vendor products: `application/vnd.measurementvendor.v1.1+json`;
 *  - brands OCM: `application/vnd.ocmbrands.v1.3+json`.
 *
 * Paginação: `nextToken` + `maxResults` (default 10) nas listagens.
 */
class DspMeasurementMethods extends BaseMethods
{
    public const STUDY_V1_2 = 'application/vnd.studymanagement.v1.2+json';

    public const STUDY_V1_3 = 'application/vnd.studymanagement.v1.3+json';

    public const ELIGIBILITY_V1_1 = 'application/vnd.measurementeligibility.v1.1+json';

    public const ELIGIBILITY_V1_2 = 'application/vnd.measurementeligibility.v1.2+json';

    public const ELIGIBILITY_V1_3 = 'application/vnd.measurementeligibility.v1.3+json';

    public const RESULT_V1_1 = 'application/vnd.measurementresult.v1.1+json';

    public const RESULT_V1_2 = 'application/vnd.measurementresult.v1.2+json';

    public const RESULT_V1_3 = 'application/vnd.measurementresult.v1.3+json';

    public const VENDOR_V1_1 = 'application/vnd.measurementvendor.v1.1+json';

    public const OCM_BRANDS_V1_3 = 'application/vnd.ocmbrands.v1.3+json';

    /** @return array<string, string> */
    private function accountHeaders(?string $adsAccountId): array
    {
        return $adsAccountId !== null ? ['Amazon-Ads-AccountId' => $adsAccountId] : [];
    }

    /** @return array<string, mixed> */
    private function pageQuery(?string $nextToken, ?int $maxResults): array
    {
        return array_filter(['nextToken' => $nextToken, 'maxResults' => $maxResults], static fn ($v) => $v !== null);
    }

    /**
     * @param  list<string>|null  $ids
     * @return array<string, mixed>
     */
    private function studiesQuery(string $idsParam, ?array $ids, ?string $advertiserId, ?string $nextToken, ?int $maxResults): array
    {
        return array_filter([
            $idsParam => $ids ? implode(',', $ids) : null,
            'advertiserId' => $advertiserId,
        ] + $this->pageQuery($nextToken, $maxResults), static fn ($v) => $v !== null);
    }

    // -------------------------------------------------------------------
    // Elegibilidade
    // -------------------------------------------------------------------

    /**
     * Elegibilidade AUDIENCE_RESEARCH contra os vendor products.
     * POST /dsp/measurement/eligibility/audienceResearch (v1.2).
     *
     * @param  array<string, mixed>  $body  audienceTargetingGroup, fundingTypeFilters, vendorProductIdFilters, vendorTypeFilters
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function checkAudienceResearchEligibility(string $profileId, array $body, ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null): array
    {
        return $this->request(method: 'POST', path: '/dsp/measurement/eligibility/audienceResearch', profileId: $profileId, query: $this->pageQuery($nextToken, $maxResults), body: $body, contentType: self::ELIGIBILITY_V1_2, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Elegibilidade BRAND_LIFT contra os vendor products.
     * POST /dsp/measurement/eligibility/brandLift (v1.1).
     *
     * @param  array<string, mixed>  $body  orderIds, excludedLineItemIds, currentStudyId, fundingTypeFilters, vendorProductIdFilters, vendorTypeFilters
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function checkBrandLiftEligibility(string $profileId, array $body, ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null, string $mediaType = self::ELIGIBILITY_V1_1): array
    {
        return $this->request(method: 'POST', path: '/dsp/measurement/eligibility/brandLift', profileId: $profileId, query: $this->pageQuery($nextToken, $maxResults), body: $body, contentType: $mediaType, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Elegibilidade CREATIVE_TESTING contra os vendor products.
     * POST /dsp/measurement/eligibility/creativeTesting (v1.2).
     *
     * @param  array<string, mixed>  $body  audienceTargetingGroup, fundingTypeFilters, vendorProductIdFilters, vendorTypeFilters
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function checkCreativeTestingEligibility(string $profileId, array $body, ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null): array
    {
        return $this->request(method: 'POST', path: '/dsp/measurement/eligibility/creativeTesting', profileId: $profileId, query: $this->pageQuery($nextToken, $maxResults), body: $body, contentType: self::ELIGIBILITY_V1_2, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Elegibilidade OMNICHANNEL_METRICS contra os vendor products.
     * POST /dsp/measurement/eligibility/omnichannelMetrics (v1.3).
     *
     * @param  array<string, mixed>  $body  orderIds, brandIds, excludedLineItemIds, currentStudyId, fundingTypeFilters, vendorProductIdFilters, vendorTypeFilters
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function checkOmnichannelMetricsEligibility(string $profileId, array $body, ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null, string $mediaType = self::ELIGIBILITY_V1_3): array
    {
        return $this->request(method: 'POST', path: '/dsp/measurement/eligibility/omnichannelMetrics', profileId: $profileId, query: $this->pageQuery($nextToken, $maxResults), body: $body, contentType: $mediaType, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Elegibilidade de planejamento contra TODOS os vendor products.
     * POST /measurement/planning/eligibility (v1.3).
     *
     * @param  array<string, mixed>  $body  advertiserId, locale (PT_BR…), orderMetadata, studyTypeFilters, fundingTypeFilters, vendorProductIdFilters, vendorTypeFilters
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function checkPlanningEligibility(string $profileId, array $body, ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null, string $mediaType = self::ELIGIBILITY_V1_3): array
    {
        return $this->request(method: 'POST', path: '/measurement/planning/eligibility', profileId: $profileId, query: $this->pageQuery($nextToken, $maxResults), body: $body, contentType: $mediaType, headers: $this->accountHeaders($adsAccountId));
    }

    // -------------------------------------------------------------------
    // Audience Research
    // -------------------------------------------------------------------

    /**
     * Estudos AUDIENCE_RESEARCH por ids ou advertiserId (um dos dois).
     * GET /dsp/measurement/studies/audienceResearch (v1.2).
     *
     * @param  list<string>|null  $studyIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getAudienceResearchStudies(string $profileId, ?array $studyIds = null, ?string $advertiserId = null, ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null): array
    {
        return $this->request(method: 'GET', path: '/dsp/measurement/studies/audienceResearch', profileId: $profileId, query: $this->studiesQuery('studyIds', $studyIds, $advertiserId, $nextToken, $maxResults), accept: self::STUDY_V1_2, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Cria estudo AUDIENCE_RESEARCH (um por chamada).
     * POST /dsp/measurement/studies/audienceResearch (v1.2).
     *
     * @param  array<string, mixed>  $study  name, advertiserId, vendorProductId, brandName, peerNames, productCategory, audienceTargetingGroup, startDate, endDate, status (DRAFT|PENDING), surveyId, externalReferenceId
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createAudienceResearchStudy(string $profileId, array $study, ?string $adsAccountId = null): array
    {
        return $this->request(method: 'POST', path: '/dsp/measurement/studies/audienceResearch', profileId: $profileId, body: $study, contentType: self::STUDY_V1_2, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Atualiza (full update) estudo AUDIENCE_RESEARCH.
     * PUT /dsp/measurement/studies/audienceResearch/{studyId} (v1.2).
     *
     * @param  array<string, mixed>  $study  mesmo shape do create
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateAudienceResearchStudy(string $profileId, string $studyId, array $study, ?string $adsAccountId = null): array
    {
        return $this->request(method: 'PUT', path: '/dsp/measurement/studies/audienceResearch/'.rawurlencode($studyId), profileId: $profileId, body: $study, contentType: self::STUDY_V1_2, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Resultado de estudo AUDIENCE_RESEARCH.
     * GET /dsp/measurement/studies/audienceResearch/{studyId}/result (measurementresult v1.2).
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getAudienceResearchStudyResult(string $profileId, string $studyId, ?string $adsAccountId = null): array
    {
        return $this->request(method: 'GET', path: '/dsp/measurement/studies/audienceResearch/'.rawurlencode($studyId).'/result', profileId: $profileId, accept: self::RESULT_V1_2, headers: $this->accountHeaders($adsAccountId));
    }

    // -------------------------------------------------------------------
    // Brand Lift
    // -------------------------------------------------------------------

    /**
     * Estudos BRAND_LIFT por ids (`studyIdFilters`) ou advertiserId.
     * GET /dsp/measurement/studies/brandLift (v1.3).
     *
     * @param  list<string>|null  $studyIdFilters
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getBrandLiftStudies(string $profileId, ?array $studyIdFilters = null, ?string $advertiserId = null, ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null, string $accept = self::STUDY_V1_3): array
    {
        return $this->request(method: 'GET', path: '/dsp/measurement/studies/brandLift', profileId: $profileId, query: $this->studiesQuery('studyIdFilters', $studyIdFilters, $advertiserId, $nextToken, $maxResults), accept: $accept, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Cria estudos BRAND_LIFT em lote.
     * POST /dsp/measurement/studies/brandLift (v1.3).
     *
     * @param  list<array<string, mixed>>  $studies  cada item: name, advertiserId, vendorProductId, orderIds, excludedLineItemIds, brandName, peerNames, productCategory, benchmarkCategory, startDate, endDate, status (DRAFT|PENDING), submissionType, surveyId, externalReferenceId
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createBrandLiftStudies(string $profileId, array $studies, ?string $adsAccountId = null, string $mediaType = self::STUDY_V1_3): array
    {
        return $this->request(method: 'POST', path: '/dsp/measurement/studies/brandLift', profileId: $profileId, body: $studies, contentType: $mediaType, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Atualiza (full update) estudos BRAND_LIFT em lote — cada item exige `id`.
     * PUT /dsp/measurement/studies/brandLift (v1.3).
     *
     * @param  list<array<string, mixed>>  $studies
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateBrandLiftStudies(string $profileId, array $studies, ?string $adsAccountId = null, string $mediaType = self::STUDY_V1_3): array
    {
        return $this->request(method: 'PUT', path: '/dsp/measurement/studies/brandLift', profileId: $profileId, body: $studies, contentType: $mediaType, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Resultado de estudo BRAND_LIFT.
     * GET /measurement/studies/brandLift/{studyId}/result (measurementresult v1.1).
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getBrandLiftStudyResult(string $profileId, string $studyId, ?string $adsAccountId = null, string $accept = self::RESULT_V1_1): array
    {
        return $this->request(method: 'GET', path: '/measurement/studies/brandLift/'.rawurlencode($studyId).'/result', profileId: $profileId, accept: $accept, headers: $this->accountHeaders($adsAccountId));
    }

    // -------------------------------------------------------------------
    // Creative Testing
    // -------------------------------------------------------------------

    /**
     * Estudos CREATIVE_TESTING por ids ou advertiserId.
     * GET /dsp/measurement/studies/creativeTesting (v1.2).
     *
     * @param  list<string>|null  $studyIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getCreativeTestingStudies(string $profileId, ?array $studyIds = null, ?string $advertiserId = null, ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null): array
    {
        return $this->request(method: 'GET', path: '/dsp/measurement/studies/creativeTesting', profileId: $profileId, query: $this->studiesQuery('studyIds', $studyIds, $advertiserId, $nextToken, $maxResults), accept: self::STUDY_V1_2, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Cria estudo CREATIVE_TESTING (um por chamada).
     * POST /dsp/measurement/studies/creativeTesting (v1.2).
     *
     * @param  array<string, mixed>  $study  name, advertiserId, vendorProductId, assets, audienceTargetingGroup, brandName, productCategory, startDate, endDate, status, surveyId, externalReferenceId
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createCreativeTestingStudy(string $profileId, array $study, ?string $adsAccountId = null): array
    {
        return $this->request(method: 'POST', path: '/dsp/measurement/studies/creativeTesting', profileId: $profileId, body: $study, contentType: self::STUDY_V1_2, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Atualiza (full update) estudo CREATIVE_TESTING.
     * PUT /dsp/measurement/studies/creativeTesting/{studyId} (v1.2).
     *
     * @param  array<string, mixed>  $study
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateCreativeTestingStudy(string $profileId, string $studyId, array $study, ?string $adsAccountId = null): array
    {
        return $this->request(method: 'PUT', path: '/dsp/measurement/studies/creativeTesting/'.rawurlencode($studyId), profileId: $profileId, body: $study, contentType: self::STUDY_V1_2, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Resultado de estudo CREATIVE_TESTING.
     * GET /dsp/measurement/studies/creativeTesting/{studyId}/result (measurementresult v1.2).
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getCreativeTestingStudyResult(string $profileId, string $studyId, ?string $adsAccountId = null): array
    {
        return $this->request(method: 'GET', path: '/dsp/measurement/studies/creativeTesting/'.rawurlencode($studyId).'/result', profileId: $profileId, accept: self::RESULT_V1_2, headers: $this->accountHeaders($adsAccountId));
    }

    // -------------------------------------------------------------------
    // Omnichannel Metrics (OCM)
    // -------------------------------------------------------------------

    /**
     * Estudos OMNICHANNEL_METRICS por ids ou advertiserId.
     * GET /dsp/measurement/studies/omnichannelMetrics (v1.3).
     *
     * @param  list<string>|null  $studyIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getOmnichannelMetricsStudies(string $profileId, ?array $studyIds = null, ?string $advertiserId = null, ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null, string $accept = self::STUDY_V1_3): array
    {
        return $this->request(method: 'GET', path: '/dsp/measurement/studies/omnichannelMetrics', profileId: $profileId, query: $this->studiesQuery('studyIds', $studyIds, $advertiserId, $nextToken, $maxResults), accept: $accept, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Cria estudos OMNICHANNEL_METRICS em lote.
     * POST /dsp/measurement/studies/omnichannelMetrics (v1.3).
     *
     * @param  list<array<string, mixed>>  $studies  cada item: name, advertiserId, vendorProductId, orderIds, excludedLineItemIds, brandIds, startDate, endDate, status, submissionType, externalReferenceId
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createOmnichannelMetricsStudies(string $profileId, array $studies, ?string $adsAccountId = null, string $mediaType = self::STUDY_V1_3): array
    {
        return $this->request(method: 'POST', path: '/dsp/measurement/studies/omnichannelMetrics', profileId: $profileId, body: $studies, contentType: $mediaType, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Atualiza (full update) estudos OMNICHANNEL_METRICS em lote — cada item exige `id`.
     * PUT /dsp/measurement/studies/omnichannelMetrics (v1.3).
     *
     * @param  list<array<string, mixed>>  $studies
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateOmnichannelMetricsStudies(string $profileId, array $studies, ?string $adsAccountId = null, string $mediaType = self::STUDY_V1_3): array
    {
        return $this->request(method: 'PUT', path: '/dsp/measurement/studies/omnichannelMetrics', profileId: $profileId, body: $studies, contentType: $mediaType, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Resultado de estudo OMNICHANNEL_METRICS.
     * GET /dsp/measurement/studies/omnichannelMetrics/{studyId}/result (measurementresult v1.3).
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getOmnichannelMetricsStudyResult(string $profileId, string $studyId, ?string $adsAccountId = null, string $accept = self::RESULT_V1_3): array
    {
        return $this->request(method: 'GET', path: '/dsp/measurement/studies/omnichannelMetrics/'.rawurlencode($studyId).'/result', profileId: $profileId, accept: $accept, headers: $this->accountHeaders($adsAccountId));
    }

    // -------------------------------------------------------------------
    // Estudos genéricos + surveys
    // -------------------------------------------------------------------

    /**
     * Estudos-base (qualquer tipo) por ids ou advertiserId.
     * GET /measurement/studies (v1.3).
     *
     * @param  list<string>|null  $studyIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getStudies(string $profileId, ?array $studyIds = null, ?string $advertiserId = null, ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null, string $accept = self::STUDY_V1_3): array
    {
        return $this->request(method: 'GET', path: '/measurement/studies', profileId: $profileId, query: $this->studiesQuery('studyIds', $studyIds, $advertiserId, $nextToken, $maxResults), accept: $accept, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Cancela estudos (irreversível). DELETE /measurement/studies?studyIds=… (v1.3).
     *
     * @param  list<string>  $studyIds
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function cancelStudies(string $profileId, array $studyIds, ?string $adsAccountId = null, string $accept = self::STUDY_V1_3): array
    {
        return $this->request(method: 'DELETE', path: '/measurement/studies', profileId: $profileId, query: ['studyIds' => implode(',', $studyIds)], accept: $accept, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Surveys por ids (`surveyIds`) ou por studyId (um dos dois).
     * GET /measurement/studies/surveys (v1.3).
     *
     * @param  list<string>|null  $surveyIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getSurveys(string $profileId, ?array $surveyIds = null, ?string $studyId = null, ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null, string $accept = self::STUDY_V1_3): array
    {
        $query = array_filter([
            'surveyIds' => $surveyIds ? implode(',', $surveyIds) : null,
            'studyId' => $studyId,
        ] + $this->pageQuery($nextToken, $maxResults), static fn ($v) => $v !== null);

        return $this->request(method: 'GET', path: '/measurement/studies/surveys', profileId: $profileId, query: $query, accept: $accept, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Cria surveys em lote (o estudo precisa existir antes).
     * POST /measurement/studies/surveys (v1.3).
     *
     * @param  list<array<string, mixed>>  $surveys  cada item: studyId, vendorProductId, templatedQuestions, customQuestions, status (DRAFT)
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createSurveys(string $profileId, array $surveys, ?string $adsAccountId = null, string $mediaType = self::STUDY_V1_3): array
    {
        return $this->request(method: 'POST', path: '/measurement/studies/surveys', profileId: $profileId, body: $surveys, contentType: $mediaType, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Atualiza (full update) surveys em lote — cada item exige `id`.
     * PUT /measurement/studies/surveys (v1.3).
     *
     * @param  list<array<string, mixed>>  $surveys
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateSurveys(string $profileId, array $surveys, ?string $adsAccountId = null, string $mediaType = self::STUDY_V1_3): array
    {
        return $this->request(method: 'PUT', path: '/measurement/studies/surveys', profileId: $profileId, body: $surveys, contentType: $mediaType, headers: $this->accountHeaders($adsAccountId));
    }

    // -------------------------------------------------------------------
    // Vendor products
    // -------------------------------------------------------------------

    /**
     * Lista vendor products de medição suportados (com filtros no body).
     * POST /measurement/vendorProducts/list (measurementvendor v1.1).
     *
     * @param  array<string, mixed>  $body  adTypeFilters, fundingTypeFilters, objectiveTypeFilters, studyTypeFilters, vendorProductIdFilters, vendorTypeFilters
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listVendorProducts(string $profileId, array $body = [], ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null): array
    {
        return $this->request(method: 'POST', path: '/measurement/vendorProducts/list', profileId: $profileId, query: $this->pageQuery($nextToken, $maxResults), body: $body, contentType: self::VENDOR_V1_1, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Busca marcas do catálogo OCM (por ids ou por texto — um dos dois).
     * POST /measurement/vendorProducts/omnichannelMetrics/brands/list (ocmbrands v1.3).
     *
     * @param  array<string, mixed>  $body  brandIdFilter (lista) OU brandNameSearch (texto)
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function omnichannelMetricsBrandSearch(string $profileId, array $body, ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null, string $mediaType = self::OCM_BRANDS_V1_3): array
    {
        return $this->request(method: 'POST', path: '/measurement/vendorProducts/omnichannelMetrics/brands/list', profileId: $profileId, query: $this->pageQuery($nextToken, $maxResults), body: $body, contentType: $mediaType, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Políticas dos vendor products. GET /measurement/vendorProducts/policies (v1.1).
     *
     * @param  list<string>|null  $vendorProductIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function vendorProductPolicies(string $profileId, ?array $vendorProductIds = null, ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null): array
    {
        $query = array_filter([
            'vendorProductIds' => $vendorProductIds ? implode(',', $vendorProductIds) : null,
        ] + $this->pageQuery($nextToken, $maxResults), static fn ($v) => $v !== null);

        return $this->request(method: 'GET', path: '/measurement/vendorProducts/policies', profileId: $profileId, query: $query, accept: self::VENDOR_V1_1, headers: $this->accountHeaders($adsAccountId));
    }

    /**
     * Templates de perguntas de survey dos vendor products.
     * GET /measurement/vendorProducts/surveyQuestionTemplates (v1.1).
     *
     * @param  list<string>|null  $vendorProductIds
     * @param  list<string>|null  $surveyQuestionTemplateIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function vendorProductSurveyQuestionTemplates(string $profileId, ?array $vendorProductIds = null, ?array $surveyQuestionTemplateIds = null, ?string $nextToken = null, ?int $maxResults = null, ?string $adsAccountId = null): array
    {
        $query = array_filter([
            'vendorProductIds' => $vendorProductIds ? implode(',', $vendorProductIds) : null,
            'surveyQuestionTemplateIds' => $surveyQuestionTemplateIds ? implode(',', $surveyQuestionTemplateIds) : null,
        ] + $this->pageQuery($nextToken, $maxResults), static fn ($v) => $v !== null);

        return $this->request(method: 'GET', path: '/measurement/vendorProducts/surveyQuestionTemplates', profileId: $profileId, query: $query, accept: self::VENDOR_V1_1, headers: $this->accountHeaders($adsAccountId));
    }
}
