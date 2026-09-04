<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Creatives;

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Bases\BaseMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;

/**
 * Creative Asset Library (v3) da Amazon Ads API: biblioteca de imagens/vídeos
 * usada por Sponsored Brands, Stores e DSP.
 *
 * Fluxo de criação: getUploadLocation() → uploadAsset() (PUT binário na URL
 * pré-assinada, válida 15 min) → registerAsset() (ou assetsBatchRegister()
 * + getAssetsBatchRegister() pra acompanhar). Tudo leva Scope (profileId);
 * `Amazon-Ads-AccountId` é a alternativa pra contas DSP.
 */
class CreativeAssetsMethods extends BaseMethods
{
    /**
     * Um asset (todas as versões, ou só `$version`) — GET /assets?assetId=…
     * Accept `application/vnd.creativeassetsgetresponse.v3+json`.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getAsset(string $profileId, string $assetId, ?string $version = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/assets',
            profileId: $profileId,
            query: array_filter(['assetId' => $assetId, 'version' => $version]),
            accept: 'application/vnd.creativeassetsgetresponse.v3+json',
        );
    }

    /**
     * Status de um registro em lote (GET /assets/batchRegister/{requestId}).
     * Accept `application/vnd.assetsbatchregisterresponse.v1+json`.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getAssetsBatchRegister(string $profileId, string $requestId): array
    {
        return $this->request(
            method: 'GET',
            path: '/assets/batchRegister/'.rawurlencode($requestId),
            profileId: $profileId,
            accept: 'application/vnd.assetsbatchregisterresponse.v1+json',
        );
    }

    /**
     * Registra vários assets já enviados (POST /assets/batchRegister) —
     * assíncrono, devolve requestId. Media type
     * `application/vnd.assetsbatchregisterrequest.v1+json`.
     *
     * @param  array<string, mixed>  $body  {assetDetailsList: [{url, name, assetType: IMAGE|VIDEO, assetSubTypeList?, asinList?, tagList?, associatedPrograms?, versionInfo?}], batchRegistrationContext?}
     * @return array<string, mixed>  {requestId, …}
     *
     * @throws AmazonAdsRequestException
     */
    public function assetsBatchRegister(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/assets/batchRegister',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.assetsbatchregisterrequest.v1+json',
        );
    }

    /**
     * Registra um asset já enviado pra URL de upload (POST /assets/register).
     * Accept `application/vnd.creativeassetsregisterresponse.v3+json`.
     *
     * @param  array<string, mixed>  $body  {url, name, assetType: IMAGE|VIDEO, assetSubTypeList?, asinList?, tags?, versionInfo?, registrationContext?: {associatedPrograms}, associatedSubEntityList?, skipAssetSubTypesDetection?}
     * @return array<string, mixed>  {assetId, versionId, …}
     *
     * @throws AmazonAdsRequestException
     */
    public function registerAsset(string $profileId, array $body, ?string $accountId = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/assets/register',
            profileId: $profileId,
            body: $body,
            contentType: 'application/json',
            accept: 'application/vnd.creativeassetsregisterresponse.v3+json',
            headers: array_filter(['Amazon-Ads-AccountId' => $accountId]),
        );
    }

    /**
     * Cria a URL pré-assinada de upload (POST /assets/upload), válida 15 min e
     * só aceita PUT. Accept `application/vnd.creativeassetsuploadresponse.v3+json`.
     *
     * @return array<string, mixed>  {url}
     *
     * @throws AmazonAdsRequestException
     */
    public function getUploadLocation(string $profileId, string $fileName, ?string $accountId = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/assets/upload',
            profileId: $profileId,
            body: ['fileName' => $fileName],
            contentType: 'application/json',
            accept: 'application/vnd.creativeassetsuploadresponse.v3+json',
            headers: array_filter(['Amazon-Ads-AccountId' => $accountId]),
        );
    }

    /**
     * Envia o binário pra URL devolvida por getUploadLocation() (PUT cru, sem
     * headers da Ads API — a URL é pré-assinada). Depois registre com
     * registerAsset() passando a mesma `url`.
     *
     * @throws AmazonAdsRequestException
     */
    public function uploadAsset(string $uploadUrl, string $contents, string $contentType = 'application/octet-stream'): void
    {
        $response = Http::withBody($contents, $contentType)->put($uploadUrl);

        if (! $response->successful()) {
            throw new AmazonAdsRequestException('Amazon Ads upload do asset falhou: '.$response->body(), $response->status());
        }
    }

    /**
     * Busca assets da biblioteca (POST /assets/search). Accept
     * `application/vnd.creativeassetssearchassetsresponse.v3+json`; paginação
     * por `pageCriteria.identifier` (token devolvido em nextToken).
     *
     * @param  array<string, mixed>  $body  {text?, filterCriteria?: {valueFilters, rangeFilters}, sortCriteria?: {field, order}, pageCriteria?: {size, identifier}}
     * @return array<string, mixed>  {assetList: [...], nextToken?}
     *
     * @throws AmazonAdsRequestException
     */
    public function searchAssets(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/assets/search',
            profileId: $profileId,
            body: $body,
            contentType: 'application/json',
            accept: 'application/vnd.creativeassetssearchassetsresponse.v3+json',
        );
    }
}
