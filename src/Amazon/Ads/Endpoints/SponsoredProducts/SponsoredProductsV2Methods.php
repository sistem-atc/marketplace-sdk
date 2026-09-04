<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredProducts;

use SistemAtc\Marketplaces\Amazon\Ads\Bases\BaseMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;

/**
 * Sponsored Products v2 LEGADO (`/v2/sp/*`) — só o que ainda não tem
 * equivalente v3: recomendação de lance por ad group/keyword/target,
 * keywords sugeridas por ad group/ASIN e snapshots.
 *
 * Tudo aqui é `application/json` puro, com Scope (profileId) no header.
 * A Amazon descontinua o v2 por partes; prefira SponsoredProductsMethods
 * (v3) sempre que houver equivalente (bid → getThemeBasedBidRecommendationForAdGroup,
 * keywords → getRankedKeywordRecommendation).
 *
 * @deprecated Sponsored Products v2 legado — use SponsoredProductsMethods (v3) quando houver equivalente.
 */
class SponsoredProductsV2Methods extends BaseMethods
{
    /**
     * Recomendação de lance pra um ad group.
     *
     * @return array<string, mixed>  {adGroupId, suggestedBid {rangeStart, rangeEnd, suggested}}
     *
     * @throws AmazonAdsRequestException
     *
     * @deprecated v2 legado
     */
    public function getAdGroupBidRecommendations(string $profileId, string $adGroupId): array
    {
        return $this->request(
            method: 'GET',
            path: '/v2/sp/adGroups/'.rawurlencode($adGroupId).'/bidRecommendations',
            profileId: $profileId,
        );
    }

    /**
     * Recomendação de lance pra uma keyword.
     *
     * @return array<string, mixed>  {keywordId, suggestedBid}
     *
     * @throws AmazonAdsRequestException
     *
     * @deprecated v2 legado
     */
    public function getKeywordBidRecommendations(string $profileId, string $keywordId): array
    {
        return $this->request(
            method: 'GET',
            path: '/v2/sp/keywords/'.rawurlencode($keywordId).'/bidRecommendations',
            profileId: $profileId,
        );
    }

    /**
     * Recomendação de lance pra keywords ainda não criadas (até 5000).
     *
     * @param  list<array{keyword: string, matchType: string}>  $keywords
     * @return array<string, mixed>  {adGroupId, recommendations: [{keyword, matchType, suggestedBid}]}
     *
     * @throws AmazonAdsRequestException
     *
     * @deprecated v2 legado
     */
    public function createKeywordBidRecommendations(string $profileId, string $adGroupId, array $keywords): array
    {
        return $this->request(
            method: 'POST',
            path: '/v2/sp/keywords/bidRecommendations',
            profileId: $profileId,
            body: ['adGroupId' => $adGroupId, 'keywords' => array_values($keywords)],
        );
    }

    /**
     * Keywords sugeridas pra um ad group.
     *
     * @param  string|null  $adStateFilter  enabled|paused|archived (CSV)
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     *
     * @deprecated v2 legado
     */
    public function getAdGroupSuggestedKeywords(string $profileId, string $adGroupId, ?int $maxNumSuggestions = null, ?string $adStateFilter = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/v2/sp/adGroups/'.rawurlencode($adGroupId).'/suggested/keywords',
            profileId: $profileId,
            query: array_filter(['maxNumSuggestions' => $maxNumSuggestions, 'adStateFilter' => $adStateFilter], static fn ($v) => $v !== null),
        );
    }

    /**
     * Keywords sugeridas pra um ad group, formato estendido (com lance sugerido).
     *
     * @param  string|null  $suggestBids  yes|no
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     *
     * @deprecated v2 legado
     */
    public function getAdGroupSuggestedKeywordsEx(string $profileId, string $adGroupId, ?int $maxNumSuggestions = null, ?string $suggestBids = null, ?string $adStateFilter = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/v2/sp/adGroups/'.rawurlencode($adGroupId).'/suggested/keywords/extended',
            profileId: $profileId,
            query: array_filter([
                'maxNumSuggestions' => $maxNumSuggestions,
                'suggestBids' => $suggestBids,
                'adStateFilter' => $adStateFilter,
            ], static fn ($v) => $v !== null),
        );
    }

    /**
     * Keywords sugeridas pra um ASIN.
     *
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     *
     * @deprecated v2 legado
     */
    public function getAsinSuggestedKeywords(string $profileId, string $asin, ?int $maxNumSuggestions = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/v2/sp/asins/'.rawurlencode($asin).'/suggested/keywords',
            profileId: $profileId,
            query: $maxNumSuggestions !== null ? ['maxNumSuggestions' => $maxNumSuggestions] : [],
        );
    }

    /**
     * Keywords sugeridas pra vários ASINs de uma vez.
     *
     * @param  list<string>  $asins
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     *
     * @deprecated v2 legado
     */
    public function bulkGetAsinSuggestedKeywords(string $profileId, array $asins, ?int $maxNumSuggestions = null): array
    {
        $body = ['asins' => array_values($asins)];

        if ($maxNumSuggestions !== null) {
            $body['maxNumSuggestions'] = $maxNumSuggestions;
        }

        return $this->request(
            method: 'POST',
            path: '/v2/sp/asins/suggested/keywords',
            profileId: $profileId,
            body: $body,
        );
    }

    /**
     * Recomendação de lance pra expressões de segmentação de um ad group
     * (todas do mesmo tipo: keyword, product ou auto).
     *
     * @param  list<list<array{type: string, value?: string}>>  $expressions
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     *
     * @deprecated v2 legado
     */
    public function getBidRecommendations(string $profileId, string $adGroupId, array $expressions): array
    {
        return $this->request(
            method: 'POST',
            path: '/v2/sp/targets/bidRecommendations',
            profileId: $profileId,
            body: ['adGroupId' => $adGroupId, 'expressions' => array_values($expressions)],
        );
    }

    /**
     * Pede um snapshot (export completo) de um tipo de entidade.
     *
     * @param  string  $recordType  campaigns|adGroups|keywords|negativeKeywords|campaignNegativeKeywords|productAds|targets|negativeTargets
     * @param  array<string, mixed>  $body  stateFilter ("enabled, paused, archived"…)
     * @return array<string, mixed>  {snapshotId, recordType, status}
     *
     * @throws AmazonAdsRequestException
     *
     * @deprecated v2 legado
     */
    public function requestSnapshot(string $profileId, string $recordType, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/v2/sp/'.rawurlencode($recordType).'/snapshot',
            profileId: $profileId,
            body: $body,
        );
    }

    /**
     * Estado do snapshot: {snapshotId, status: IN_PROGRESS|SUCCESS|FAILURE, location?}.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     *
     * @deprecated v2 legado
     */
    public function getSnapshotStatus(string $profileId, string $snapshotId): array
    {
        return $this->request(
            method: 'GET',
            path: '/v2/sp/snapshots/'.rawurlencode($snapshotId),
            profileId: $profileId,
        );
    }

    /**
     * Baixa o snapshot. A API responde 307 pro S3; o client segue o redirect
     * e devolve o JSON (lista de entidades) já decodificado (gzip tolerado).
     *
     * @return list<array<string, mixed>>
     *
     * @throws AmazonAdsRequestException
     *
     * @deprecated v2 legado
     */
    public function downloadSnapshot(string $profileId, string $snapshotId): array
    {
        $path = '/v2/sp/snapshots/'.rawurlencode($snapshotId).'/download';

        $response = $this->http($profileId)
            ->withOptions(['decode_content' => false])
            ->get($this->baseUrl().$path);

        if (! $response->successful()) {
            throw new AmazonAdsRequestException("Amazon Ads GET {$path}: ".$response->body(), $response->status());
        }

        $body = (string) $response->body();
        $json = @gzdecode($body);

        if ($json === false) {
            $json = $body;
        }

        $rows = json_decode($json, true);

        if (! is_array($rows)) {
            throw new AmazonAdsRequestException("snapshot {$snapshotId} baixado não é JSON válido");
        }

        return $rows;
    }
}
