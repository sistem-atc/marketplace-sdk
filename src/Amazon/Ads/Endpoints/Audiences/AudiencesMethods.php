<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Audiences;

use SistemAtc\Marketplaces\Amazon\Ads\Bases\BaseMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;

/**
 * Audiências da Amazon Ads API: discovery (segmentos + taxonomia), edição de
 * audiências DSP e a API de Data Provider (v2/dp — hashed records).
 *
 * Discovery/DSP levam Scope (profileId). Data Provider NÃO leva Scope: é a
 * conta do provedor de dados (ClientId + Bearer), com limite de 1 TPS
 * (100 TPS no PATCH /v2/dp/audience).
 */
class AudiencesMethods extends BaseMethods
{
    // ----------------------------------------------------------------- Discovery

    /**
     * Segmentos de audiência por filtros (POST /audiences/list).
     * Paginação por `nextToken`/`maxResults` na query. `advertiserId` (query)
     * e `Amazon-Ads-AccountId` são obrigatórios pro adType DSP.
     *
     * @param  array<string, mixed>  $body  {adType: DSP|SD, countries?, filters?: [{field, operator, values}]}
     * @param  array<string, mixed>  $query  advertiserId?, canTarget?, nextToken?, maxResults?
     * @return array<string, mixed>  {audiences: [...], nextToken?}
     *
     * @throws AmazonAdsRequestException
     */
    public function listAudiences(string $profileId, array $body, array $query = [], ?string $accountId = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/audiences/list',
            profileId: $profileId,
            query: $query,
            body: $body,
            contentType: 'application/json',
            headers: array_filter(['Amazon-Ads-AccountId' => $accountId]),
        );
    }

    /**
     * Navega a taxonomia de categorias de audiência (POST /audiences/taxonomy/list).
     * Paginação por `nextToken`/`maxResults` na query.
     *
     * @param  array<string, mixed>  $body  {adType: DSP|SD, categoryPath?: [...], countries?}
     * @param  array<string, mixed>  $query  advertiserId?, nextToken?, maxResults?
     * @return array<string, mixed>  {categoryPath, categories: [...], nextToken?}
     *
     * @throws AmazonAdsRequestException
     */
    public function fetchTaxonomy(string $profileId, array $body, array $query = [], ?string $accountId = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/audiences/taxonomy/list',
            profileId: $profileId,
            query: $query,
            body: $body,
            contentType: 'application/json',
            headers: array_filter(['Amazon-Ads-AccountId' => $accountId]),
        );
    }

    // ----------------------------------------------------------------- DSP audiences

    /**
     * Exclui audiências de targeting DSP (POST /dsp/audiences/delete).
     * Header `AdvertiserId` (anunciante DSP) + media type
     * `application/vnd.dspaudiences.v1+json`.
     *
     * @param  list<array<string, mixed>>  $items  cada um {audienceId, idempotencyKey}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function dspAudienceDelete(string $profileId, string $advertiserId, array $items): array
    {
        return $this->request(
            method: 'POST',
            path: '/dsp/audiences/delete',
            profileId: $profileId,
            body: ['dspAudienceDeleteRequestItems' => array_values($items)],
            contentType: 'application/vnd.dspaudiences.v1+json',
            headers: ['AdvertiserId' => $advertiserId],
        );
    }

    /**
     * Edita audiências de targeting DSP (PUT /dsp/audiences/edit).
     * Header `AdvertiserId` + media type `application/vnd.dspaudiences.v1+json`.
     *
     * @param  list<array<string, mixed>>  $items  cada um {audienceId, audienceType, idempotencyKey, name?, description?, lookback?, rules?}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function dspAudienceEdit(string $profileId, string $advertiserId, array $items): array
    {
        return $this->request(
            method: 'PUT',
            path: '/dsp/audiences/edit',
            profileId: $profileId,
            body: ['dspAudienceEditRequestItems' => array_values($items)],
            contentType: 'application/vnd.dspaudiences.v1+json',
            headers: ['AdvertiserId' => $advertiserId],
        );
    }

    // ----------------------------------------------------------------- Data Provider (v2/dp)

    /**
     * Cria uma audiência de data provider (POST /v2/dp/audiencemetadata/). Sem Scope; 1 TPS.
     *
     * @param  array<string, mixed>  $body  {name, description, advertiserId, metadata: {type: DATA_PROVIDER, externalAudienceId, dataSourceCountry: [...], ttl?, audienceFees?}}
     * @return array<string, mixed>  {audienceId, …}
     *
     * @throws AmazonAdsRequestException
     */
    public function createAudienceMetadata(array $body): array
    {
        return $this->request(method: 'POST', path: '/v2/dp/audiencemetadata/', body: $body, contentType: 'application/json');
    }

    /**
     * Atualiza metadados de uma audiência de data provider
     * (PUT /v2/dp/audiencemetadata/{audienceId}). Sem Scope; 1 TPS.
     *
     * @param  array<string, mixed>  $body  {description?, metadata?: {ttl?, audienceFees?, dataSourceCountry?}}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateAudienceMetadata(string $audienceId, array $body): array
    {
        return $this->request(
            method: 'PUT',
            path: '/v2/dp/audiencemetadata/'.rawurlencode($audienceId),
            body: $body,
            contentType: 'application/json',
        );
    }

    /**
     * Metadados de uma audiência de data provider
     * (GET /v2/dp/audiencemetadata/{audienceId}). Sem Scope; 1 TPS.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getAudienceMetadata(string $audienceId): array
    {
        return $this->request(method: 'GET', path: '/v2/dp/audiencemetadata/'.rawurlencode($audienceId));
    }

    /**
     * Associa/desassocia registros (hashed) a audiências (PATCH /v2/dp/audience). Sem Scope; 100 TPS.
     *
     * @param  list<array<string, mixed>>  $patches  cada um {op: add|remove, path: /audienceId, value: [{hashedPII?, …}], consent?}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function patchAudienceRecords(array $patches): array
    {
        return $this->request(
            method: 'PATCH',
            path: '/v2/dp/audience',
            body: ['patches' => array_values($patches)],
            contentType: 'application/json',
        );
    }

    /**
     * Apaga dados de usuários originados do cliente (PATCH /v2/dp/users). Sem Scope; 1 TPS.
     *
     * @param  array<string, mixed>  $body  {users: [{userId: {…}, advertiserId?, consentTime?}], operation: DELETE}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function patchUsers(array $body): array
    {
        return $this->request(method: 'PATCH', path: '/v2/dp/users', body: $body, contentType: 'application/json');
    }
}
