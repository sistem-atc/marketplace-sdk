<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredBrands;

use SistemAtc\Marketplaces\Amazon\Ads\Bases\BaseMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;

/**
 * Sponsored Brands — rotas que só existem na spec v3 (`sponsored-brands_3-0_openapi`):
 * keywords, negative keywords, product targets, negative targets, themes,
 * recomendações de lance/alvo, brands, stores/assets, mídia, moderação e os
 * relatórios v2 legados (`/v2/hsa`).
 *
 * Exige `Amazon-Advertising-API-Scope` em tudo. Listagens antigas (keywords)
 * paginam por startIndex/count; as novas (targets, themes, recommendations)
 * por `nextToken` no body. Respostas usam media types próprios em Accept
 * (`application/vnd.sbkeyword.v3.2+json` etc.); o Content-Type de escrita é
 * `application/json` puro.
 *
 * Campanhas/grupos/anúncios ficam em SponsoredBrandsMethods (v4).
 */
class SponsoredBrandsV3Methods extends BaseMethods
{
    public const ACCEPT_KEYWORD = 'application/vnd.sbkeyword.v3.2+json';

    public const ACCEPT_KEYWORD_RESPONSE = 'application/vnd.sbkeywordresponse.v3+json';

    public const ACCEPT_NEGATIVE_KEYWORD = 'application/vnd.sbnegativekeyword.v3.2+json';

    // ------------------------------------------------------------------
    // Brands / ASINs / mídia / assets
    // ------------------------------------------------------------------

    /**
     * GET /brands — marcas do anunciante (brandEntityId usado nas campanhas).
     *
     * @return list<array<string, mixed>>|array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getBrands(string $profileId, ?string $brandTypeFilter = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/brands',
            profileId: $profileId,
            query: $brandTypeFilter !== null ? ['brandTypeFilter' => $brandTypeFilter] : [],
            accept: 'application/vnd.brand.v3+json',
        );
    }

    /**
     * GET /pageAsins — ASINs de uma página de Store / landing page.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listAsins(string $profileId, string $pageUrl): array
    {
        return $this->request(
            method: 'GET',
            path: '/pageAsins',
            profileId: $profileId,
            query: ['pageUrl' => $pageUrl],
            accept: 'application/vnd.pageasins.v3+json',
        );
    }

    /**
     * GET /media/describe — status de processamento de uma mídia (vídeo) enviada.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function describeMedia(string $profileId, string $mediaId): array
    {
        return $this->request(
            method: 'GET',
            path: '/media/describe',
            profileId: $profileId,
            query: ['mediaId' => $mediaId],
        );
    }

    /**
     * GET /stores/assets — assets (logos) da marca.
     *
     * @return list<array<string, mixed>>|array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listAssets(string $profileId, ?string $brandEntityId = null, ?string $mediaType = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/stores/assets',
            profileId: $profileId,
            query: array_filter(['brandEntityId' => $brandEntityId, 'mediaType' => $mediaType], fn ($v) => $v !== null),
            accept: 'application/vnd.mediaasset.v3+json',
        );
    }

    /**
     * POST /stores/assets — sobe um asset de imagem (brandLogo; < 1 MB, mínimo 400×400).
     *
     * O corpo é o BINÁRIO puro da imagem; o tipo vai em Content-Type
     * (image/png, image/jpeg…) e o nome do arquivo em Content-Disposition.
     * `assetInfo` ({brandEntityId?, mediaType}) vai como header JSON-encoded.
     *
     * @param  array<string, mixed>  $assetInfo  ex.: ['mediaType' => 'brandLogo', 'brandEntityId' => 'ENTITY…']
     * @return array<string, mixed>  {assetId…}
     *
     * @throws AmazonAdsRequestException
     */
    public function createAsset(string $profileId, string $binary, string $fileName, string $mimeType, array $assetInfo = ['mediaType' => 'brandLogo']): array
    {
        $response = $this->http($profileId)
            ->withHeaders([
                'Content-Disposition' => $fileName,
                'assetInfo' => (string) json_encode($assetInfo),
            ])
            ->withBody($binary, $mimeType)
            ->post($this->baseUrl().'/stores/assets');

        return $this->decodeOrFail($response, 'POST /stores/assets');
    }

    // ------------------------------------------------------------------
    // Keywords
    // ------------------------------------------------------------------

    /**
     * GET /sb/keywords — keywords filtradas; pagina por startIndex/count.
     *
     * @param  array<string, mixed>  $query  startIndex, count, matchTypeFilter, keywordText, stateFilter, campaignIdFilter, adGroupIdFilter, keywordIdFilter, creativeType, locale
     * @return list<array<string, mixed>>|array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listKeywords(string $profileId, array $query = []): array
    {
        return $this->request(
            method: 'GET',
            path: '/sb/keywords',
            profileId: $profileId,
            query: $query,
            accept: self::ACCEPT_KEYWORD,
        );
    }

    /**
     * POST /sb/keywords — cria keywords (lista pura no body).
     *
     * @param  list<array<string, mixed>>  $keywords  campaignId, adGroupId, keywordText, matchType, bid, nativeLanguageKeyword, nativeLanguageLocale
     * @return list<array<string, mixed>>|array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createKeywords(string $profileId, array $keywords): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/keywords',
            profileId: $profileId,
            body: array_values($keywords),
            accept: self::ACCEPT_KEYWORD_RESPONSE,
        );
    }

    /**
     * PUT /sb/keywords — atualiza keywords (keywordId, adGroupId, campaignId obrigatórios; state, bid).
     *
     * @param  list<array<string, mixed>>  $keywords
     * @return list<array<string, mixed>>|array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateKeywords(string $profileId, array $keywords): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sb/keywords',
            profileId: $profileId,
            body: array_values($keywords),
            accept: self::ACCEPT_KEYWORD_RESPONSE,
        );
    }

    /**
     * GET /sb/keywords/{keywordId} — uma keyword.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getKeyword(string $profileId, string $keywordId, ?string $locale = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/sb/keywords/'.rawurlencode($keywordId),
            profileId: $profileId,
            query: $locale !== null ? ['locale' => $locale] : [],
            accept: 'application/vnd.sbkeyword.v3+json',
        );
    }

    /**
     * DELETE /sb/keywords/{keywordId} — arquiva a keyword (irreversível).
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function archiveKeyword(string $profileId, string $keywordId): array
    {
        return $this->request(
            method: 'DELETE',
            path: '/sb/keywords/'.rawurlencode($keywordId),
            profileId: $profileId,
            accept: self::ACCEPT_KEYWORD_RESPONSE,
        );
    }

    // ------------------------------------------------------------------
    // Negative keywords
    // ------------------------------------------------------------------

    /**
     * GET /sb/negativeKeywords — negative keywords filtradas; pagina por startIndex/count.
     *
     * @param  array<string, mixed>  $query  startIndex, count, matchTypeFilter, keywordText, stateFilter, campaignIdFilter, adGroupIdFilter, keywordIdFilter, creativeType
     * @return list<array<string, mixed>>|array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listNegativeKeywords(string $profileId, array $query = []): array
    {
        return $this->request(
            method: 'GET',
            path: '/sb/negativeKeywords',
            profileId: $profileId,
            query: $query,
            accept: self::ACCEPT_NEGATIVE_KEYWORD,
        );
    }

    /**
     * POST /sb/negativeKeywords — cria negative keywords.
     *
     * @param  list<array<string, mixed>>  $keywords  campaignId, adGroupId, keywordText, matchType
     * @return list<array<string, mixed>>|array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createNegativeKeywords(string $profileId, array $keywords): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/negativeKeywords',
            profileId: $profileId,
            body: array_values($keywords),
            accept: self::ACCEPT_KEYWORD_RESPONSE,
        );
    }

    /**
     * PUT /sb/negativeKeywords — atualiza negative keywords (keywordId, adGroupId, campaignId, state).
     *
     * @param  list<array<string, mixed>>  $keywords
     * @return list<array<string, mixed>>|array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateNegativeKeywords(string $profileId, array $keywords): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sb/negativeKeywords',
            profileId: $profileId,
            body: array_values($keywords),
            accept: self::ACCEPT_KEYWORD_RESPONSE,
        );
    }

    /**
     * GET /sb/negativeKeywords/{keywordId} — uma negative keyword.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getNegativeKeyword(string $profileId, string $keywordId): array
    {
        return $this->request(
            method: 'GET',
            path: '/sb/negativeKeywords/'.rawurlencode($keywordId),
            profileId: $profileId,
            accept: 'application/vnd.sbnegativekeyword.v3+json',
        );
    }

    /**
     * DELETE /sb/negativeKeywords/{keywordId} — arquiva a negative keyword.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function archiveNegativeKeyword(string $profileId, string $keywordId): array
    {
        return $this->request(
            method: 'DELETE',
            path: '/sb/negativeKeywords/'.rawurlencode($keywordId),
            profileId: $profileId,
            accept: self::ACCEPT_KEYWORD_RESPONSE,
        );
    }

    // ------------------------------------------------------------------
    // Product targets
    // ------------------------------------------------------------------

    /**
     * POST /sb/targets/list — lista product targets; pagina por nextToken no body.
     *
     * @param  array<string, mixed>  $body  nextToken, maxResults, filters[] ({filterType, values[]})
     * @return array<string, mixed>  {targets: [...], nextToken?}
     *
     * @throws AmazonAdsRequestException
     */
    public function listTargets(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/targets/list',
            profileId: $profileId,
            body: $body,
            accept: 'application/vnd.sblisttargetsresponse.v3.2+json',
        );
    }

    /**
     * POST /sb/targets — cria product targets.
     *
     * @param  list<array<string, mixed>>  $targets  campaignId, adGroupId, expressions[], bid
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createTargets(string $profileId, array $targets): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/targets',
            profileId: $profileId,
            body: ['targets' => array_values($targets)],
            accept: 'application/vnd.sbcreatetargetsresponse.v3+json',
        );
    }

    /**
     * PUT /sb/targets — atualiza product targets (targetId, adGroupId, campaignId, state, bid).
     *
     * @param  list<array<string, mixed>>  $targets
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateTargets(string $profileId, array $targets): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sb/targets',
            profileId: $profileId,
            body: ['targets' => array_values($targets)],
            accept: 'application/vnd.updatetargetsresponse.v3+json',
        );
    }

    /**
     * GET /sb/targets/{targetId} — um product target.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getTarget(string $profileId, string $targetId): array
    {
        return $this->request(
            method: 'GET',
            path: '/sb/targets/'.rawurlencode($targetId),
            profileId: $profileId,
            accept: 'application/vnd.sbtarget.v3+json',
        );
    }

    /**
     * DELETE /sb/targets/{targetId} — arquiva o target (irreversível).
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function archiveTarget(string $profileId, string $targetId): array
    {
        return $this->request(
            method: 'DELETE',
            path: '/sb/targets/'.rawurlencode($targetId),
            profileId: $profileId,
            accept: 'application/vnd.sbtargetresponse.v3+json',
        );
    }

    // ------------------------------------------------------------------
    // Negative targets
    // ------------------------------------------------------------------

    /**
     * POST /sb/negativeTargets/list — lista negative targets; pagina por nextToken no body.
     *
     * @param  array<string, mixed>  $body  nextToken, maxResults, filters[]
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listNegativeTargets(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/negativeTargets/list',
            profileId: $profileId,
            body: $body,
            accept: 'application/vnd.sblistnegativetargetsresponse.v3.2+json',
        );
    }

    /**
     * POST /sb/negativeTargets — cria negative targets.
     *
     * @param  list<array<string, mixed>>  $negativeTargets  campaignId, adGroupId, expressions[]
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createNegativeTargets(string $profileId, array $negativeTargets): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/negativeTargets',
            profileId: $profileId,
            body: ['negativeTargets' => array_values($negativeTargets)],
            accept: 'application/vnd.sbcreatenegativetargetsrequest.v3+json',
        );
    }

    /**
     * PUT /sb/negativeTargets — atualiza negative targets (targetId, adGroupId, state).
     *
     * @param  list<array<string, mixed>>  $negativeTargets
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateNegativeTargets(string $profileId, array $negativeTargets): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sb/negativeTargets',
            profileId: $profileId,
            body: ['negativeTargets' => array_values($negativeTargets)],
            accept: 'application/vnd.updatenegativetargetsresponse.v3+json',
        );
    }

    /**
     * GET /sb/negativeTargets/{negativeTargetId} — um negative target.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getNegativeTarget(string $profileId, string $negativeTargetId): array
    {
        return $this->request(
            method: 'GET',
            path: '/sb/negativeTargets/'.rawurlencode($negativeTargetId),
            profileId: $profileId,
            accept: 'application/vnd.sbnegativetarget.v3+json',
        );
    }

    /**
     * DELETE /sb/negativeTargets/{negativeTargetId} — arquiva o negative target (irreversível).
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function archiveNegativeTarget(string $profileId, string $negativeTargetId): array
    {
        return $this->request(
            method: 'DELETE',
            path: '/sb/negativeTargets/'.rawurlencode($negativeTargetId),
            profileId: $profileId,
            accept: 'application/vnd.sbnegativetarget.v3+json',
        );
    }

    // ------------------------------------------------------------------
    // Themes (theme targeting)
    // ------------------------------------------------------------------

    /**
     * POST /sb/themes/list — lista theme targets; pagina por nextToken no body. Não suporta perfis Author.
     *
     * @param  array<string, mixed>  $body  nextToken, maxResults, themeIdFilter, adGroupIdFilter, campaignIdFilter, stateFilter, themeTypeFilter
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listThemes(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/themes/list',
            profileId: $profileId,
            body: $body,
            accept: 'application/vnd.sbthemeslistresponse.v3+json',
        );
    }

    /**
     * POST /sb/themes — cria theme targets.
     *
     * @param  list<array<string, mixed>>  $themes  adGroupId, campaignId, themeType, bid
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createThemes(string $profileId, array $themes): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/themes',
            profileId: $profileId,
            body: ['themes' => array_values($themes)],
            accept: 'application/vnd.sbthemescreateresponse.v3+json',
        );
    }

    /**
     * PUT /sb/themes — atualiza theme targets (themeId, adGroupId, state, bid).
     *
     * @param  list<array<string, mixed>>  $themes
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateThemes(string $profileId, array $themes): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sb/themes',
            profileId: $profileId,
            body: ['themes' => array_values($themes)],
            accept: 'application/vnd.sbthemesupdateresponse.v3+json',
        );
    }

    // ------------------------------------------------------------------
    // Recomendações v3 (lance e alvos)
    // ------------------------------------------------------------------

    /**
     * POST /sb/recommendations/bids — recomendação de lance pra keywords/targets.
     *
     * @param  array<string, mixed>  $body  campaignId, keywords[] | targets[], adFormat, costType, themeTypes[], goal
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getBidsRecommendations(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/recommendations/bids',
            profileId: $profileId,
            body: $body,
            accept: 'application/vnd.sbbidsrecommendation.v3+json',
        );
    }

    /**
     * POST /sb/recommendations/targets/product/list — produtos recomendados pra segmentação; pagina por nextToken.
     *
     * @param  array<string, mixed>  $body  nextToken, maxResults, filters[]
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getProductRecommendations(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/recommendations/targets/product/list',
            profileId: $profileId,
            body: $body,
            accept: 'application/vnd.sbproductrecommendationsresponse.v3.1+json',
        );
    }

    /**
     * POST /sb/recommendations/targets/category — categorias recomendadas pros ASINs.
     *
     * @param  list<string>  $asins
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getTargetingCategories(string $profileId, array $asins, ?string $supplySource = null, ?string $locale = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/recommendations/targets/category',
            profileId: $profileId,
            query: $locale !== null ? ['locale' => $locale] : [],
            body: array_filter(['asins' => array_values($asins), 'supplySource' => $supplySource], fn ($v) => $v !== null),
            accept: 'application/vnd.sbcategoryrecommendationsresponse.v3.2+json',
        );
    }

    /**
     * POST /sb/recommendations/targets/brand — marcas sugeridas; body com `categoryId` OU `keyword`.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getBrandRecommendations(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/recommendations/targets/brand',
            profileId: $profileId,
            body: $body,
            accept: 'application/vnd.sbbrandrecommendationsresponse.v3.1+json',
        );
    }

    // ------------------------------------------------------------------
    // Moderação (deprecated na spec) e relatórios v2 legados
    // ------------------------------------------------------------------

    /**
     * GET /sb/moderation/campaigns/{campaignId} — resultado de moderação da campanha.
     *
     * @deprecated marcado como deprecated na spec v3; use preModeration()/listCreatives() em SponsoredBrandsMethods.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getCampaignModeration(string $profileId, string $campaignId): array
    {
        return $this->request(
            method: 'GET',
            path: '/sb/moderation/campaigns/'.rawurlencode($campaignId),
            profileId: $profileId,
            accept: 'application/vnd.sbmoderation.v3+json',
        );
    }

    /**
     * POST /v2/hsa/{recordType}/report — solicita relatório v2 legado (recordType: campaigns, adGroups, keywords, targets…).
     *
     * @deprecated reporting v2 legado; use ReportingMethods (v3, reportTypeId sbCampaigns).
     *
     * @param  array<string, mixed>  $options  segment, creativeType
     * @return array<string, mixed>  {reportId, status…}
     *
     * @throws AmazonAdsRequestException
     */
    public function requestReport(string $profileId, string $recordType, string $reportDate, string $metrics, array $options = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/v2/hsa/'.rawurlencode($recordType).'/report',
            profileId: $profileId,
            body: ['reportDate' => $reportDate, 'metrics' => $metrics] + $options,
            contentType: 'application/json',
        );
    }

    /**
     * GET /v2/reports/{reportId} — status do relatório v2 legado.
     *
     * @deprecated reporting v2 legado; use ReportingMethods::getReport().
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getReport(string $profileId, string $reportId): array
    {
        return $this->request(
            method: 'GET',
            path: '/v2/reports/'.rawurlencode($reportId),
            profileId: $profileId,
            accept: 'application/json',
        );
    }

    /**
     * GET /v2/reports/{reportId}/download — baixa o relatório v2 legado (307 → S3; segue o redirect
     * e devolve as linhas do JSON, descompactando gzip se vier comprimido).
     *
     * @deprecated reporting v2 legado; use ReportingMethods::downloadReportRows().
     *
     * @return list<array<string, mixed>>
     *
     * @throws AmazonAdsRequestException
     */
    public function downloadReport(string $profileId, string $reportId): array
    {
        $response = $this->http($profileId)
            ->withOptions(['decode_content' => false])
            ->get($this->baseUrl().'/v2/reports/'.rawurlencode($reportId).'/download');

        if (! $response->successful()) {
            throw new AmazonAdsRequestException("Amazon Ads GET /v2/reports/{$reportId}/download: ".$response->body(), $response->status());
        }

        $body = (string) $response->body();
        $json = @gzdecode($body);
        $rows = json_decode($json === false ? $body : $json, true);

        if (! is_array($rows)) {
            throw new AmazonAdsRequestException("relatório v2 {$reportId} baixado não é JSON válido");
        }

        return $rows;
    }
}
