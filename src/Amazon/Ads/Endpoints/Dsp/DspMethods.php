<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Dsp;

use SistemAtc\Marketplaces\Amazon\Ads\Bases\BaseMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;

/**
 * Amazon DSP (Demand-Side Platform) — orders, line items, creatives,
 * advertisers, geolocalização e relatórios v3.
 *
 * Escopo: em todas as rotas `/dsp/*` o header `Amazon-Advertising-API-Scope`
 * é o **profileId da conta DSP** (vem de /v2/profiles com
 * accountInfo.type=agency|vendor com acesso DSP) — NÃO é o advertiserId. O
 * `advertiserId` é uma entidade abaixo do profile e viaja no path
 * (`/dsp/advertisers/{advertiserId}`), na query (`advertiserIdFilter`) ou no
 * body (orders/creatives), conforme cada operação.
 *
 * Relatórios v3 (`/accounts/{accountId}/dsp/reports`) fogem da regra: o
 * escopo vai no path como `accountId` (DSP Entity ID pra um grupo de
 * anunciantes, ou DSP Advertiser ID pra um só) e a spec não pede Scope —
 * o `$profileId` ali é opcional.
 *
 * Media types: cada família DSP tem o seu (`application/vnd.dsporders.v2.6+json`,
 * `application/vnd.dsplineitems.v3.3+json`…). Os métodos usam a versão mais
 * recente por padrão; passe `$mediaType`/`$accept` pra fixar outra.
 *
 * Paginação: orders/lineItems/creatives/advertisers usam `startIndex` +
 * `count` (offset); geoLocations usa `nextToken` + `maxResults`.
 *
 * Todo o bloco de creatives legado (image/video/rec/thirdparty, moderation,
 * lineItemCreativeAssociations) está marcado como DEPRECATED pela Amazon —
 * substituído pela Creatives API unificada (fora deste lote).
 */
class DspMethods extends BaseMethods
{
    public const ORDERS_V2_6 = 'application/vnd.dsporders.v2.6+json';

    public const CONVERSION_TRACKING_V2_1 = 'application/vnd.dsporders.v2.1+json';

    public const PRODUCT_TRACKING_V1 = 'application/vnd.dspproducttracking.v1+json';

    public const BASIC_LINE_ITEMS_V3 = 'application/vnd.dspbasiclineitems.v3+json';

    public const LINE_ITEMS_V3_3 = 'application/vnd.dsplineitems.v3.3+json';

    public const LINE_ITEMS_RESPONSE_V3_1 = 'application/vnd.dsplineitemsresponse.v3.1+json';

    public const CREATIVES_V2_1 = 'application/vnd.dspcreatives.v2.1+json';

    public const REPORTS_CREATE_V3 = 'application/vnd.dspcreatereports.v3+json';

    public const REPORTS_GET_V3 = 'application/vnd.dspgetreports.v3+json';

    // -------------------------------------------------------------------
    // Orders
    // -------------------------------------------------------------------

    /**
     * Lista orders (informação básica) da conta DSP. GET /dsp/orders/.
     * Paginação por startIndex/count. Filtros comma-delimited.
     *
     * @param  list<string>|null  $statusFilter  DELIVERING|ENDED|OUT_OF_BUDGET|LINEITEMS_NOT_RUNNING|INACTIVE|READY_TO_DELIVER…
     * @param  list<string>|null  $orderIdFilter
     * @param  list<string>|null  $advertiserIdFilter
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getOrders(
        string $profileId,
        ?int $startIndex = null,
        ?int $count = null,
        ?array $statusFilter = null,
        ?array $orderIdFilter = null,
        ?array $advertiserIdFilter = null,
        string $accept = self::ORDERS_V2_6,
    ): array {
        $query = array_filter([
            'startIndex' => $startIndex,
            'count' => $count,
            'statusFilter' => $statusFilter ? implode(',', $statusFilter) : null,
            'orderIdFilter' => $orderIdFilter ? implode(',', $orderIdFilter) : null,
            'advertiserIdFilter' => $advertiserIdFilter ? implode(',', $advertiserIdFilter) : null,
        ], static fn ($v) => $v !== null);

        return $this->request(method: 'GET', path: '/dsp/orders/', profileId: $profileId, query: $query, accept: $accept);
    }

    /**
     * Order completa por id. GET /dsp/orders/{orderId}.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getOrder(string $profileId, string $orderId, string $accept = self::ORDERS_V2_6): array
    {
        return $this->request(method: 'GET', path: '/dsp/orders/'.rawurlencode($orderId), profileId: $profileId, accept: $accept);
    }

    /**
     * Atualiza orders em lote. PUT /dsp/orders/ (resposta 207 multi-status).
     *
     * @param  list<array<string, mixed>>  $orders  cada item: orderId (imutável), advertiserId*, name*, budget*, frequencyCap*, optimization*, externalId, comments, startDateTime, endDateTime, currencyCode, agencyFee, deliveryActivationStatus (ACTIVE|INACTIVE)
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateOrders(string $profileId, array $orders, string $mediaType = self::ORDERS_V2_6): array
    {
        return $this->request(method: 'PUT', path: '/dsp/orders/', profileId: $profileId, body: $orders, contentType: $mediaType);
    }

    /**
     * Conversion tracking da order. GET /dsp/orders/{orderId}/conversionTracking.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getConversionTrackings(string $profileId, string $orderId, string $accept = self::CONVERSION_TRACKING_V2_1): array
    {
        return $this->request(method: 'GET', path: '/dsp/orders/'.rawurlencode($orderId).'/conversionTracking', profileId: $profileId, accept: $accept);
    }

    /**
     * Adiciona/remove produtos rastreados na order.
     * PUT /dsp/orders/{orderId}/conversionTracking/products (204 sem corpo).
     *
     * @param  array<string, mixed>  $body  productList (lista de {asin, action…}) e/ou productFile (URL do arquivo)
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateConversionTrackingProducts(string $profileId, string $orderId, array $body): array
    {
        return $this->request(
            method: 'PUT',
            path: '/dsp/orders/'.rawurlencode($orderId).'/conversionTracking/products',
            profileId: $profileId,
            body: $body,
            contentType: self::PRODUCT_TRACKING_V1,
            accept: 'application/vnd.dsperrors.v1+json',
        );
    }

    // -------------------------------------------------------------------
    // Line items
    // -------------------------------------------------------------------

    /**
     * Lista line items (informação básica). GET /dsp/lineItems/.
     * Paginação por startIndex/count.
     *
     * @param  list<string>|null  $statusFilter
     * @param  list<string>|null  $orderIdFilter
     * @param  list<string>|null  $lineItemIdFilter
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getLineItems(
        string $profileId,
        ?int $startIndex = null,
        ?int $count = null,
        ?array $statusFilter = null,
        ?array $orderIdFilter = null,
        ?array $lineItemIdFilter = null,
        string $accept = self::BASIC_LINE_ITEMS_V3,
    ): array {
        $query = array_filter([
            'startIndex' => $startIndex,
            'count' => $count,
            'statusFilter' => $statusFilter ? implode(',', $statusFilter) : null,
            'orderIdFilter' => $orderIdFilter ? implode(',', $orderIdFilter) : null,
            'lineItemIdFilter' => $lineItemIdFilter ? implode(',', $lineItemIdFilter) : null,
        ], static fn ($v) => $v !== null);

        return $this->request(method: 'GET', path: '/dsp/lineItems/', profileId: $profileId, query: $query, accept: $accept);
    }

    /**
     * Atualiza line items em lote. PUT /dsp/lineItems/ (207 multi-status).
     *
     * @param  list<array<string, mixed>>  $lineItems  cada item: lineItemId (imutável), orderId*, lineItemType*, name*, startDateTime*, endDateTime*, lineItemClassification*, frequencyCap*, bidding*, optimization*, targeting, budget, creativeOptions, deliveryActivationStatus…
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateLineItems(
        string $profileId,
        array $lineItems,
        string $mediaType = self::LINE_ITEMS_V3_3,
        string $accept = self::LINE_ITEMS_RESPONSE_V3_1,
    ): array {
        return $this->request(method: 'PUT', path: '/dsp/lineItems/', profileId: $profileId, body: $lineItems, contentType: $mediaType, accept: $accept);
    }

    // -------------------------------------------------------------------
    // Creatives (listagem unificada) + creatives legados por tipo
    // -------------------------------------------------------------------

    /**
     * Lista creatives (todos os tipos). GET /dsp/creatives/.
     * Paginação por startIndex/count.
     *
     * @param  list<string>|null  $creativeIdFilter
     * @param  list<string>|null  $advertiserIdFilter
     * @param  string|null  $lineItemTypeFilter  STANDARD_DISPLAY|AMAZON_MOBILE_DISPLAY|AAP_MOBILE_APP|VIDEO
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getCreatives(
        string $profileId,
        ?int $startIndex = null,
        ?int $count = null,
        ?array $creativeIdFilter = null,
        ?array $advertiserIdFilter = null,
        ?string $lineItemTypeFilter = null,
        string $accept = self::CREATIVES_V2_1,
    ): array {
        $query = array_filter([
            'startIndex' => $startIndex,
            'count' => $count,
            'creativeIdFilter' => $creativeIdFilter ? implode(',', $creativeIdFilter) : null,
            'advertiserIdFilter' => $advertiserIdFilter ? implode(',', $advertiserIdFilter) : null,
            'lineItemTypeFilter' => $lineItemTypeFilter,
        ], static fn ($v) => $v !== null);

        return $this->request(method: 'GET', path: '/dsp/creatives/', profileId: $profileId, query: $query, accept: $accept);
    }

    /**
     * Image creatives por id. GET /dsp/creatives/image.
     *
     * @deprecated Amazon marcou como DEPRECATED (usar a Creatives API unificada).
     *
     * @param  list<string>  $creativeIdFilter  obrigatório
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getImageCreatives(string $profileId, array $creativeIdFilter): array
    {
        return $this->request(
            method: 'GET',
            path: '/dsp/creatives/image',
            profileId: $profileId,
            query: ['creativeIdFilter' => implode(',', $creativeIdFilter)],
            accept: 'application/vnd.dspimagecreatives.v1+json',
        );
    }

    /**
     * Cria image creatives. POST /dsp/creatives/image.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  list<array<string, mixed>>  $creatives  cada item: name*, advertiserId*, marketplace*, size*, asset*, clickThroughAction*, supply*, externalId, thirdPartyClickTrackers, thirdPartyTrackers, additionalHtml, adChoicesPosition
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createImageCreative(string $profileId, array $creatives): array
    {
        return $this->request(
            method: 'POST',
            path: '/dsp/creatives/image',
            profileId: $profileId,
            body: $creatives,
            contentType: 'application/vnd.dspcreateimagecreatives.v1+json',
            accept: 'application/vnd.dspimagecreativesresponse.v1+json',
        );
    }

    /**
     * Atualiza image creatives. PUT /dsp/creatives/image.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  list<array<string, mixed>>  $creatives  cada item: creativeId*, name*, size*, asset*, clickThroughAction*…
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateImageCreative(string $profileId, array $creatives): array
    {
        return $this->request(
            method: 'PUT',
            path: '/dsp/creatives/image',
            profileId: $profileId,
            body: $creatives,
            contentType: 'application/vnd.dspupdateimagecreatives.v1+json',
            accept: 'application/vnd.dspimagecreativesresponse.v1+json',
        );
    }

    /**
     * Preview de image creative. POST /dsp/creatives/image/preview.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  array<string, mixed>  $body  creativeId (existente) OU creativeModel (novo) + previewConfiguration
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function previewImageCreative(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/dsp/creatives/image/preview',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.dsppreviewimagecreatives.v1+json',
            accept: 'application/vnd.dsppreviewcreativesresponse.v1+json',
        );
    }

    /**
     * Video creatives por id. GET /dsp/creatives/video.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  list<string>  $creativeIdFilter  obrigatório
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getVideoCreatives(string $profileId, array $creativeIdFilter): array
    {
        return $this->request(
            method: 'GET',
            path: '/dsp/creatives/video',
            profileId: $profileId,
            query: ['creativeIdFilter' => implode(',', $creativeIdFilter)],
            accept: 'application/vnd.dspvideocreatives.v1+json',
        );
    }

    /**
     * Cria video creatives. POST /dsp/creatives/video.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  list<array<string, mixed>>  $creatives  cada item: name*, advertiserId*, marketplace*, asset*, clickThroughAction*, externalId, thirdPartyTrackers
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createVideoCreatives(string $profileId, array $creatives): array
    {
        return $this->request(
            method: 'POST',
            path: '/dsp/creatives/video',
            profileId: $profileId,
            body: $creatives,
            contentType: 'application/vnd.dspcreatevideocreatives.v1+json',
            accept: 'application/vnd.dspvideocreativesresponse.v1+json',
        );
    }

    /**
     * Atualiza video creatives. PUT /dsp/creatives/video.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  list<array<string, mixed>>  $creatives  cada item: creativeId*, name*, asset*, clickThroughAction*…
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateVideoCreatives(string $profileId, array $creatives): array
    {
        return $this->request(
            method: 'PUT',
            path: '/dsp/creatives/video',
            profileId: $profileId,
            body: $creatives,
            contentType: 'application/vnd.dspupdatevideocreatives.v1+json',
            accept: 'application/vnd.dspvideocreativesresponse.v1+json',
        );
    }

    /**
     * Preview de video creative. POST /dsp/creatives/video/preview.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  array<string, mixed>  $body  creativeId OU creativeModel
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function previewVideoCreative(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/dsp/creatives/video/preview',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.dsppreviewvideocreatives.v1+json',
            accept: 'application/vnd.dsppreviewcreativesresponse.v1+json',
        );
    }

    /**
     * Responsive eCommerce Creatives (REC) por id. GET /dsp/creatives/rec.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  list<string>  $creativeIdFilter  obrigatório
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getRecCreatives(string $profileId, array $creativeIdFilter): array
    {
        return $this->request(
            method: 'GET',
            path: '/dsp/creatives/rec',
            profileId: $profileId,
            query: ['creativeIdFilter' => implode(',', $creativeIdFilter)],
            accept: 'application/vnd.dspreccreatives.v1+json',
        );
    }

    /**
     * Cria REC creatives. POST /dsp/creatives/rec.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  list<array<string, mixed>>  $creatives  cada item: name*, advertiserId*, marketplace*, associatedProducts*, content, allowedFormats, allowedSizes, optimizationGoal, allowThirdPartySellers, additionalHtml, thirdPartyClickTrackers, thirdPartyTrackers
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createRecCreatives(string $profileId, array $creatives): array
    {
        return $this->request(
            method: 'POST',
            path: '/dsp/creatives/rec',
            profileId: $profileId,
            body: $creatives,
            contentType: 'application/vnd.dspcreatereccreatives.v1+json',
            accept: 'application/vnd.dspreccreativesresponse.v1+json',
        );
    }

    /**
     * Atualiza REC creatives. PUT /dsp/creatives/rec.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  list<array<string, mixed>>  $creatives  cada item: creativeId*, name*, associatedProducts*…
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateRecCreatives(string $profileId, array $creatives): array
    {
        return $this->request(
            method: 'PUT',
            path: '/dsp/creatives/rec',
            profileId: $profileId,
            body: $creatives,
            contentType: 'application/vnd.dspupdatereccreatives.v1+json',
            accept: 'application/vnd.dspreccreativesresponse.v1+json',
        );
    }

    /**
     * Preview de REC creative. POST /dsp/creatives/rec/preview.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  array<string, mixed>  $body  creativeId OU creativeModel + previewConfiguration*
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function previewRecCreative(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/dsp/creatives/rec/preview',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.dsppreviewreccreatives.v1+json',
            accept: 'application/vnd.dsppreviewcreativesresponse.v1+json',
        );
    }

    /**
     * Third-party creatives por id. GET /dsp/creatives/thirdparty.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  list<string>  $creativeIdFilter  obrigatório
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getThirdPartyCreatives(string $profileId, array $creativeIdFilter): array
    {
        return $this->request(
            method: 'GET',
            path: '/dsp/creatives/thirdparty',
            profileId: $profileId,
            query: ['creativeIdFilter' => implode(',', $creativeIdFilter)],
            accept: 'application/vnd.dspthirdpartycreatives.v1+json',
        );
    }

    /**
     * Cria third-party creatives. POST /dsp/creatives/thirdparty.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  list<array<string, mixed>>  $creatives  cada item: name*, advertiserId*, marketplace*, size*, tagSource*, supply* (DESKTOP|MOBILE_OO|MOBILE_AAP), destination, externalId, thirdPartyTrackers, additionalHtml, adChoicesPosition
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createThirdPartyCreative(string $profileId, array $creatives): array
    {
        return $this->request(
            method: 'POST',
            path: '/dsp/creatives/thirdparty',
            profileId: $profileId,
            body: $creatives,
            contentType: 'application/vnd.dspcreatethirdpartycreatives.v1+json',
            accept: 'application/vnd.dspthirdpartycreativesresponse.v1+json',
        );
    }

    /**
     * Atualiza third-party creatives. PUT /dsp/creatives/thirdparty.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  list<array<string, mixed>>  $creatives  cada item: creativeId*, name*, size*, tagSource*…
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateThirdPartyCreative(string $profileId, array $creatives): array
    {
        return $this->request(
            method: 'PUT',
            path: '/dsp/creatives/thirdparty',
            profileId: $profileId,
            body: $creatives,
            contentType: 'application/vnd.dspupdatethirdpartycreatives.v1+json',
            accept: 'application/vnd.dspthirdpartycreativesresponse.v1+json',
        );
    }

    /**
     * Preview de third-party creative. POST /dsp/creatives/thirdparty/preview.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  array<string, mixed>  $body  creativeId OU creativeModel + previewConfiguration*
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function previewThirdPartyCreative(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/dsp/creatives/thirdparty/preview',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.dsppreviewthirdpartycreatives.v1+json',
            accept: 'application/vnd.dsppreviewcreativesresponse.v1+json',
        );
    }

    /**
     * Resumo de moderação por creativeId. GET /dsp/moderation/creatives.
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  list<string>  $creativeIdFilter  obrigatório
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getCreativeModeration(string $profileId, array $creativeIdFilter): array
    {
        return $this->request(
            method: 'GET',
            path: '/dsp/moderation/creatives',
            profileId: $profileId,
            query: ['creativeIdFilter' => implode(',', $creativeIdFilter)],
            accept: 'application/vnd.dspmoderationcreatives.v1+json',
        );
    }

    /**
     * Cria/remove associação line item ↔ creative.
     * POST /dsp/lineItemCreativeAssociations (v2.1).
     *
     * @deprecated Amazon marcou como DEPRECATED.
     *
     * @param  string  $operation  CREATE|DELETE
     * @param  list<array<string, mixed>>  $associations  cada item: lineItemId, creativeId (+ opcionais)
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function associateLineItemsToCreatives(string $profileId, string $advertiserId, string $operation, array $associations): array
    {
        return $this->request(
            method: 'POST',
            path: '/dsp/lineItemCreativeAssociations',
            profileId: $profileId,
            body: ['advertiserId' => $advertiserId, 'operation' => $operation, 'associations' => $associations],
            contentType: 'application/vnd.dsplineitemcreativeassociations.v2.1+json',
        );
    }

    // -------------------------------------------------------------------
    // Discovery / advertisers
    // -------------------------------------------------------------------

    /**
     * Localizações geográficas (locationTargeting) por ids ou texto.
     * GET /dsp/geoLocations. Paginação por nextToken/maxResults (default 10).
     *
     * @param  list<string>|null  $geoLocationIdFilter  até 10 por chamada
     * @param  string|null  $textQuery  cidade, estado, país, DMA ou CEP
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getGeoLocations(
        string $profileId,
        ?array $geoLocationIdFilter = null,
        ?string $textQuery = null,
        ?string $nextToken = null,
        ?int $maxResults = null,
    ): array {
        $query = array_filter([
            'geoLocationIdFilter' => $geoLocationIdFilter ? implode(',', $geoLocationIdFilter) : null,
            'textQuery' => $textQuery,
            'nextToken' => $nextToken,
            'maxResults' => $maxResults,
        ], static fn ($v) => $v !== null);

        return $this->request(method: 'GET', path: '/dsp/geoLocations', profileId: $profileId, query: $query, accept: 'application/json');
    }

    /**
     * Lista advertisers DSP visíveis pelo profile. GET /dsp/advertisers (v3.0).
     * Paginação por startIndex (default 0) / count (default 100, máx 100).
     *
     * @param  list<string>|null  $advertiserIdFilter
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getAdvertisers(string $profileId, ?int $startIndex = null, ?int $count = null, ?array $advertiserIdFilter = null): array
    {
        $query = array_filter([
            'startIndex' => $startIndex,
            'count' => $count,
            'advertiserIdFilter' => $advertiserIdFilter ? implode(',', $advertiserIdFilter) : null,
        ], static fn ($v) => $v !== null);

        return $this->request(method: 'GET', path: '/dsp/advertisers', profileId: $profileId, query: $query, accept: 'application/json');
    }

    /**
     * Advertiser DSP por id. GET /dsp/advertisers/{advertiserId} (v3.0).
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getAdvertiser(string $profileId, string $advertiserId): array
    {
        return $this->request(method: 'GET', path: '/dsp/advertisers/'.rawurlencode($advertiserId), profileId: $profileId, accept: 'application/json');
    }

    // -------------------------------------------------------------------
    // Reports v3 (/accounts/{accountId}/dsp/reports)
    // -------------------------------------------------------------------

    /**
     * Solicita relatório DSP (assíncrono). POST /accounts/{accountId}/dsp/reports.
     *
     * `$accountId` = DSP Entity ID (todos os anunciantes da entidade) ou DSP
     * Advertiser ID (um só). A spec não pede Scope aqui; `$profileId` é
     * opcional e só entra se informado. Devolve {reportId, status: IN_PROGRESS…}.
     *
     * @param  array<string, mixed>  $body  startDate*, endDate* (yyyy-MM-dd), type (CAMPAIGN|AUDIENCE|GEOGRAPHY|INVENTORY|PRODUCT|CONVERSION_SOURCE…), dimensions, metrics, format (CSV|JSON), timeUnit (DAILY|SUMMARY), advertiserIds, orderIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createReport(string $accountId, array $body, ?string $profileId = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/accounts/'.rawurlencode($accountId).'/dsp/reports',
            profileId: $profileId,
            body: $body,
            contentType: self::REPORTS_CREATE_V3,
        );
    }

    /**
     * Metadados/status do relatório DSP. GET /accounts/{accountId}/dsp/reports/{reportId}.
     * Quando status=SUCCESS, `location` traz a URL de download (baixar com
     * ReportingMethods::downloadReportRows ou HTTP puro — CSV/JSON sem gzip).
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getReport(string $accountId, string $reportId, ?string $profileId = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/accounts/'.rawurlencode($accountId).'/dsp/reports/'.rawurlencode($reportId),
            profileId: $profileId,
            accept: self::REPORTS_GET_V3,
        );
    }
}
