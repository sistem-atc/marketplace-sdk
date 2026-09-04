<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Account;

use SistemAtc\Marketplaces\Amazon\Ads\Bases\BaseMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;

/**
 * Hub de CONTA da Amazon Ads API: perfis (v2), Manager Accounts, Billing,
 * Eligibility, Localization, Change History e Product Selector.
 *
 * Perfis e Manager Accounts NÃO levam `Amazon-Advertising-API-Scope` (são o
 * nível acima do profile). O resto recebe `profileId` como primeiro argumento.
 * Rotas com media type próprio (vnd.*) mandam o mesmo em Content-Type/Accept.
 */
class AccountMethods extends BaseMethods
{
    // ----------------------------------------------------------------- Profiles (v2)

    /**
     * Lista os perfis de anunciante acessíveis pelo token (GET /v2/profiles).
     * Sem Scope. Query opcional: apiProgram, accessLevel, profileTypeFilter
     * (csv: seller,vendor,agency), validPaymentMethodFilter.
     *
     * @param  array<string, mixed>  $query
     * @return list<array<string, mixed>>
     *
     * @throws AmazonAdsRequestException
     */
    public function listProfiles(array $query = []): array
    {
        return array_values($this->request(method: 'GET', path: '/v2/profiles', query: $query));
    }

    /**
     * Atualiza o dailyBudget de um ou mais perfis (PUT /v2/profiles). Sem Scope.
     *
     * @param  list<array<string, mixed>>  $profiles  cada item: profileId + dailyBudget (…countryCode, currencyCode, timezone, accountInfo)
     * @return list<array<string, mixed>>  {profileId, code, details?} por item
     *
     * @throws AmazonAdsRequestException
     */
    public function updateProfiles(array $profiles): array
    {
        return array_values($this->request(method: 'PUT', path: '/v2/profiles', body: $profiles, contentType: 'application/json'));
    }

    /**
     * Um perfil pelo id (GET /v2/profiles/{profileId}). Sem Scope.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getProfileById(string $profileId): array
    {
        return $this->request(method: 'GET', path: '/v2/profiles/'.rawurlencode($profileId));
    }

    // ----------------------------------------------------------------- Manager Accounts

    /**
     * Manager accounts que o usuário do token acessa (GET /managerAccounts). Sem Scope.
     * Accept `application/vnd.getmanageraccountsresponse.v1+json`.
     *
     * @return array<string, mixed>  {managerAccounts: [...]}
     *
     * @throws AmazonAdsRequestException
     */
    public function getManagerAccountsForUser(): array
    {
        return $this->request(
            method: 'GET',
            path: '/managerAccounts',
            accept: 'application/vnd.getmanageraccountsresponse.v1+json',
        );
    }

    /**
     * Cria um manager account (POST /managerAccounts). Sem Scope.
     * Content-Type `application/vnd.createmanageraccountrequest.v1+json`.
     *
     * @param  array<string, mixed>  $body  managerAccountName, managerAccountType (Advertiser|Agency)
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createManagerAccount(array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/managerAccounts',
            body: $body,
            contentType: 'application/vnd.createmanageraccountrequest.v1+json',
            accept: 'application/vnd.manageraccount.v1+json',
        );
    }

    /**
     * Vincula contas/anunciantes a um manager account
     * (POST /managerAccounts/{managerAccountId}/associate). Sem Scope.
     *
     * @param  array<string, mixed>  $body  {accounts: [{id, type: ACCOUNT_ID|DSP_ADVERTISER_ID, roles: [...]}]}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function linkAdvertisingAccountsToManagerAccount(string $managerAccountId, array $body): array
    {
        return $this->managerAccountLink($managerAccountId, 'associate', $body);
    }

    /**
     * Desvincula contas/anunciantes de um manager account
     * (POST /managerAccounts/{managerAccountId}/disassociate). Sem Scope.
     *
     * @param  array<string, mixed>  $body  {accounts: [{id, type, roles}]}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function unlinkAdvertisingAccountsToManagerAccount(string $managerAccountId, array $body): array
    {
        return $this->managerAccountLink($managerAccountId, 'disassociate', $body);
    }

    /** @param array<string, mixed> $body */
    private function managerAccountLink(string $managerAccountId, string $action, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/managerAccounts/'.rawurlencode($managerAccountId).'/'.$action,
            body: $body,
            contentType: 'application/vnd.updateadvertisingaccountsinmanageraccountrequest.v1+json',
            accept: 'application/vnd.updateadvertisingaccountsinmanageraccountresponse.v1+json',
        );
    }

    // ----------------------------------------------------------------- Billing

    /**
     * Status de cobrança de várias contas (POST /billing/statuses).
     * A spec não exige Scope (consulta em lote por advertiserId); passe o
     * profileId se a conta pedir.
     *
     * @param  array<string, mixed>  $body  {advertiserMarketplaces: [{advertiserId, marketplaceId, advertiserType?}], locale?}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function bulkGetBillingStatus(array $body, ?string $profileId = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/billing/statuses',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.bulkgetbillingstatusrequestbody.v1+json',
            accept: 'application/vnd.bulkgetbillingstatusresponse.v1+json',
        );
    }

    /**
     * Notificações de cobrança de várias contas (POST /billing/notifications).
     *
     * @param  array<string, mixed>  $body  {advertiserMarketplaces: [{advertiserId, marketplaceId, advertiserType?}], locale?}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function bulkGetBillingNotifications(array $body, ?string $profileId = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/billing/notifications',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.billingnotifications.v1+json',
            accept: 'application/vnd.bulkgetbillingnotificationsresponse.v1+json',
        );
    }

    // ----------------------------------------------------------------- Eligibility

    /**
     * Elegibilidade de produtos pra anunciar (POST /eligibility/product/list).
     *
     * @param  array<string, mixed>  $body  {productDetailsList: [{asin, sku?, globalStoreSetting?}], adType?: sp|sb|sd, locale?}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function productEligibility(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/eligibility/product/list',
            profileId: $profileId,
            body: $body,
            contentType: 'application/json',
        );
    }

    /**
     * Elegibilidade do anunciante aos programas de ads (POST /eligibility/programs).
     * Default media type `application/vnd.programeligibility.v2+json` (body
     * {maxResults?, nextToken?}); passe `application/json` pro formato antigo
     * ({skipChecks}). `Amazon-Ads-AccountId` (conta global) é alternativa ao Scope.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function programEligibility(
        string $profileId,
        array $body = [],
        ?string $accountId = null,
        ?string $acceptLanguage = null,
        string $contentType = 'application/vnd.programeligibility.v2+json',
    ): array {
        $headers = array_filter([
            'Amazon-Ads-AccountId' => $accountId,
            'Accept-Language' => $acceptLanguage,
        ]);

        return $this->request(
            method: 'POST',
            path: '/eligibility/programs',
            profileId: $profileId,
            body: $body,
            contentType: $contentType,
            headers: $headers,
        );
    }

    // ----------------------------------------------------------------- Localization

    /**
     * Converte valores monetários entre marketplaces (POST /currencies/localize).
     * Media type `application/vnd.currencylocalization.{v1|v2}+json`.
     *
     * @param  array<string, mixed>  $body  {localizeCurrencyRequests: [{currency: {amount, currencyCode}}], sourceMarketplaceId|sourceCountryCode, targetMarketplaces|targetCountryCodes}
     * @param  string|null  $accountId  header `Amazon-Ads-AccountId` (obrigatório na spec; cai no profileId se omitido)
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getLocalizedCurrencies(string $profileId, array $body, ?string $accountId = null, string $version = 'v2'): array
    {
        return $this->localize('/currencies/localize', "application/vnd.currencylocalization.{$version}+json", $profileId, $body, $accountId);
    }

    /**
     * Traduz keywords entre marketplaces (POST /keywords/localize).
     * Media type `application/vnd.keywordlocalization.{v1|v2}+json`.
     *
     * @param  array<string, mixed>  $body  {localizeKeywordRequests: [{localizationKeyword: {keyword, matchType?}}], sourceDetails?, targetDetails: {marketplaceIds|countryCodes|locales}}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getLocalizedKeywords(string $profileId, array $body, ?string $accountId = null, string $version = 'v2'): array
    {
        return $this->localize('/keywords/localize', "application/vnd.keywordlocalization.{$version}+json", $profileId, $body, $accountId);
    }

    /**
     * Localiza produtos (ASIN/SKU) entre marketplaces (POST /products/localize).
     * Media type `application/vnd.productlocalization.{v1|v2}+json`.
     *
     * @param  array<string, mixed>  $body  {adType, entityType, sourceAdvertiserId, sourceMarketplaceId|sourceCountryCode, localizeProductRequests: [{product: {asin|sku}}], targetDetails: [{advertiserId, marketplaceId|countryCode}]}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getLocalizedProducts(string $profileId, array $body, ?string $accountId = null, string $version = 'v2'): array
    {
        return $this->localize('/products/localize', "application/vnd.productlocalization.{$version}+json", $profileId, $body, $accountId);
    }

    /**
     * Localiza expressões de targeting (POST /targetingExpression/localize).
     * Media type `application/vnd.targetingexpressionlocalization.{v1|v2}+json`.
     *
     * @param  array<string, mixed>  $body  {requests: [{targetingExpression}], sourceDetails: {marketplaceId|countryCode}, targetDetailsList: [{marketplaceId|countryCode}], sourceResolvedTargetingExpressionLocales?, targetResolvedTargetingExpressionLocale?}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getLocalizedTargetingExpression(string $profileId, array $body, ?string $accountId = null, string $version = 'v2'): array
    {
        return $this->localize('/targetingExpression/localize', "application/vnd.targetingexpressionlocalization.{$version}+json", $profileId, $body, $accountId);
    }

    /** @param array<string, mixed> $body */
    private function localize(string $path, string $mediaType, string $profileId, array $body, ?string $accountId): array
    {
        return $this->request(
            method: 'POST',
            path: $path,
            profileId: $profileId,
            body: $body,
            contentType: $mediaType,
            headers: ['Amazon-Ads-AccountId' => $accountId ?? $profileId],
        );
    }

    // ----------------------------------------------------------------- Change history

    /**
     * Histórico de alterações das entidades do anunciante (POST /history).
     * Datas em epoch ms UTC; paginação por `nextToken`.
     *
     * @param  array<string, mixed>  $body  {fromDate, toDate, eventTypes: {CAMPAIGN: {eventTypeIds?, changeTypes?}, AD_GROUP…}, count?, nextToken?, pageOffset?, sort?}
     * @return array<string, mixed>  {events: [...], nextToken?}
     *
     * @throws AmazonAdsRequestException
     */
    public function getHistory(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/history',
            profileId: $profileId,
            body: $body,
            contentType: 'application/json',
        );
    }

    // ----------------------------------------------------------------- Product selector

    /**
     * Metadados (e elegibilidade) dos produtos do anunciante (POST /product/metadata).
     * Content-Type `application/vnd.productmetadatarequest.v1+json`; paginação
     * por pageIndex/pageSize (ou cursorToken).
     *
     * @param  array<string, mixed>  $body  {pageIndex, pageSize, asins?|skus?|searchStr?, adType?, checkEligibility?, checkItemDetails?, locale?, sortBy?, sortOrder?}
     * @param  string|null  $accountId  header `Amazon-Ads-AccountId` (DSP)
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function productMetadata(string $profileId, array $body, ?string $accountId = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/product/metadata',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.productmetadatarequest.v1+json',
            accept: 'application/vnd.productmetadataresponse.v1+json',
            headers: array_filter(['Amazon-Ads-AccountId' => $accountId]),
        );
    }
}
