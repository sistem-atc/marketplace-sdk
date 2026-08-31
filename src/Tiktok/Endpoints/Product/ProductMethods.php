<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Product;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalCategoryListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalCategoryRecommendResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalCategoryAttributesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalCategoryRulesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalListingRulesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalReplicateProductResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalReplicatedProductsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalProductCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalProductDeleteResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalProductEditResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalProductPublishResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalProductResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalProductSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ImageTranslationTaskCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ImageTranslationTaskListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ManufacturerCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ManufacturerSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ResponsiblePersonCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ResponsiblePersonSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\UploadedProductFileResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\UploadedProductImageResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\DiagnoseOptimizeResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\InventorySearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ListingPrerequisitesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\OpportunityDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\OpportunityListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\OpportunitySubmitResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\OptimizedImagesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductAuditingResearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductDiagnosesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductListingCheckResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductOperationResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductSeoWordsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductSkppDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductSkppStatusListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\RecommendCategoryResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\RecommendedProductPackageResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\RecommendedTitleDescriptionResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ShopSkppSummaryResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\SizeChartSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\StockOperationSettingsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\SubmissionRecordsResponseDTO;

class ProductMethods extends BaseMethods
{
    /** Versao da API de produto (mesma convencao de /order/{version}/...). */
    private const VERSION = '202309';

    /**
     * Busca produtos do seller. page_size e page_token vao na QUERY (a API
     * recusa page_size no body); os filtros (status, etc) vao no body.
     */
    public function search(array $filters = [], int $pageSize = 20, ?string $pageToken = null): ProductSearchResponseDTO
    {
        $query = ['page_size' => $pageSize];
        if ($pageToken !== null && $pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/product/'.self::VERSION.'/products/search', $query, $filters);

        return ProductSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    public function get(string $productId): ProductResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/product/'.self::VERSION."/products/{$productId}");

        return ProductResponseDTO::fromArray($response['data'] ?? []);
    }

    public function updatePrice(string $productId, array $skus): array
    {
        return $this->makeRequest(HttpMethod::POST, '/product/'.self::VERSION."/products/{$productId}/prices/update", [], [
            'skus' => $skus,
        ]);
    }

    public function updateStock(string $productId, array $skus): array
    {
        return $this->makeRequest(HttpMethod::POST, '/product/'.self::VERSION."/products/{$productId}/stocks/update", [], [
            'skus' => $skus,
        ]);
    }

    // ---------------------------------------------------------------------
    // Catalogo: estoque
    // ---------------------------------------------------------------------

    /**
     * Estoque detalhado por SKU/armazem — inclui o COMPROMETIDO (reservado por
     * pedido em aberto) e o rateio por campanha/criador, que o `get()` nao traz.
     * Sem paginacao: devolve exatamente os IDs pedidos (max 100 produtos /
     * 600 SKUs). Se mandar os dois, `sku_ids` tem precedencia.
     *
     * @param  list<string>  $productIds
     * @param  list<string>  $skuIds
     */
    public function searchInventory(array $productIds = [], array $skuIds = []): InventorySearchResponseDTO
    {
        $body = [];
        if ($productIds !== []) {
            $body['product_ids'] = array_values($productIds);
        }
        if ($skuIds !== []) {
            $body['sku_ids'] = array_values($skuIds);
        }

        $response = $this->makeRequest(HttpMethod::POST, '/product/'.self::VERSION.'/inventory/search', [], $body);

        return InventorySearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Liga/desliga a reposicao automatica de estoque no cancelamento (so' FBM).
     * Operacao PARCIAL: confira `failedSkuIds` — code 0 nao garante todos.
     *
     * @param  list<string>  $skuIds  max 500 por chamada
     */
    public function updateStockOperationSettings(string $sellerId, array $skuIds, bool $enableAutoRestock): StockOperationSettingsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/product/202604/inventory/operation/settings', [], [
            'seller_id' => $sellerId,
            'sku_ids' => array_values($skuIds),
            'stock_operation_setting' => ['enable_auto_restock' => $enableAutoRestock],
        ]);

        return StockOperationSettingsResponseDTO::fromArray($response['data'] ?? []);
    }

    // ---------------------------------------------------------------------
    // Ciclo de vida do anuncio
    // ---------------------------------------------------------------------

    /**
     * Reativa produtos (max 20 IDs). Sucesso vem VAZIO: a resposta so' lista o
     * que falhou — `errors` vazio significa que todos passaram.
     *
     * @param  list<string>  $productIds
     * @param  list<string>  $listingPlatforms  TIKTOK_SHOP (default) / TOKOPEDIA
     */
    public function activate(array $productIds, array $listingPlatforms = []): ProductOperationResponseDTO
    {
        return $this->productLifecycleOperation('activate', $productIds, $listingPlatforms);
    }

    /**
     * Desativa produtos (max 20 IDs). Mesma semantica de erro do activate.
     *
     * @param  list<string>  $productIds
     * @param  list<string>  $listingPlatforms
     */
    public function deactivate(array $productIds, array $listingPlatforms = []): ProductOperationResponseDTO
    {
        return $this->productLifecycleOperation('deactivate', $productIds, $listingPlatforms);
    }

    /**
     * Recupera produtos deletados (max 20 IDs). Nao aceita listing_platforms.
     *
     * @param  list<string>  $productIds
     */
    public function recover(array $productIds): ProductOperationResponseDTO
    {
        return $this->productLifecycleOperation('recover', $productIds);
    }

    /**
     * @param  list<string>  $productIds
     * @param  list<string>  $listingPlatforms
     */
    private function productLifecycleOperation(string $action, array $productIds, array $listingPlatforms = []): ProductOperationResponseDTO
    {
        $body = ['product_ids' => array_values($productIds)];
        if ($listingPlatforms !== []) {
            $body['listing_platforms'] = array_values($listingPlatforms);
        }

        $response = $this->makeRequest(HttpMethod::POST, '/product/'.self::VERSION."/products/{$action}", [], $body);

        return ProductOperationResponseDTO::fromArray($response['data'] ?? []);
    }

    // ---------------------------------------------------------------------
    // Pre-voo da publicacao
    // ---------------------------------------------------------------------

    /**
     * Pre-requisitos da LOJA pra publicar (armazem de devolucao, template de
     * frete, conta bancaria...). Semantica invertida: `isFailed` = true bloqueia.
     */
    public function checkListingPrerequisites(): ListingPrerequisitesResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/product/202312/prerequisites');

        return ListingPrerequisitesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Valida um payload de Create Product SEM criar o produto. Vale rodar antes
     * de todo publish — o payload e' o mesmo do `create`.
     *
     * @param  array<string, mixed>  $product
     */
    public function checkListing(array $product): ProductListingCheckResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/product/'.self::VERSION.'/products/listing_check', [], $product);

        return ProductListingCheckResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Categoria sugerida a partir de titulo/descricao/imagens. Use
     * `leafCategoryId` no Create Product — produto so' entra em categoria folha.
     *
     * @param  array<string, mixed>  $payload  title, description, images[], category_version, locale...
     */
    public function recommendCategory(array $payload): RecommendCategoryResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/product/'.self::VERSION.'/categories/recommend', [], $payload);

        return RecommendCategoryResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Tabelas de medidas da loja. Pagina por page_token na QUERY; `ids` tem
     * precedencia sobre `keyword` quando os dois vao juntos.
     *
     * @param  list<string>  $ids     max 50
     * @param  list<string>  $locales idiomas das imagens a retornar
     */
    public function searchSizeCharts(array $ids = [], ?string $keyword = null, int $pageSize = 20, ?string $pageToken = null, array $locales = []): SizeChartSearchResponseDTO
    {
        $query = ['page_size' => $pageSize];
        if ($pageToken !== null && $pageToken !== '') {
            $query['page_token'] = $pageToken;
        }
        if ($locales !== []) {
            $query['locales'] = implode(',', $locales);
        }

        $body = [];
        if ($ids !== []) {
            $body['ids'] = array_values($ids);
        }
        if ($keyword !== null && $keyword !== '') {
            $body['keyword'] = $keyword;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/product/202407/sizecharts/search', $query, $body);

        return SizeChartSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    // ---------------------------------------------------------------------
    // Qualidade do anuncio (diagnostico e sugestao)
    // ---------------------------------------------------------------------

    /**
     * Diagnostica um produto — existente OU ainda nao publicado (basta omitir
     * `product_id` no payload). Pedir DESCRIPTION em `optimization_fields`
     * pode levar mais de 10s pra responder.
     *
     * @param  array<string, mixed>  $payload
     */
    public function diagnoseAndOptimize(array $payload): DiagnoseOptimizeResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/product/202411/products/diagnose_optimize', [], $payload);

        return DiagnoseOptimizeResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Diagnostico em lote de produtos JA publicados. Os IDs vao na QUERY
     * separados por virgula — nao no body.
     *
     * @param  list<string>  $productIds
     */
    public function getDiagnoses(array $productIds): ProductDiagnosesResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/product/202405/products/diagnoses', [
            'product_ids' => implode(',', $productIds),
        ]);

        return ProductDiagnosesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Palavras-chave de SEO sugeridas pro titulo. IDs na QUERY, por virgula.
     *
     * @param  list<string>  $productIds
     */
    public function getSeoWords(array $productIds): ProductSeoWordsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/product/202405/products/seo_words', [
            'product_ids' => implode(',', $productIds),
        ]);

        return ProductSeoWordsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Titulo/descricao sugeridos pela IA da plataforma. IDs na QUERY, por virgula.
     *
     * @param  list<string>  $productIds
     */
    public function getSuggestedTitleAndDescription(array $productIds): RecommendedTitleDescriptionResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/product/202405/products/suggestions', [
            'product_ids' => implode(',', $productIds),
        ]);

        return RecommendedTitleDescriptionResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Otimiza imagens ja' enviadas (hoje so' WHITE_BACKGROUND, exigido pela 1a
     * imagem da galeria). Max 200 por chamada; status PROCESSING = chamar de novo.
     *
     * @param  list<array{uri: string, optimization_mode?: list<string>}>  $images
     */
    public function optimizeImages(array $images): OptimizedImagesResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/product/202404/images/optimize', [], [
            'images' => array_values($images),
        ]);

        return OptimizedImagesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Peso/dimensoes recomendados pra produtos com medida anomala — medida
     * errada encarece o frete cobrado do vendedor.
     *
     * @param  list<string>  $productIds
     */
    public function getRecommendedPackages(array $productIds = [], int $pageSize = 100, ?string $pageToken = null): RecommendedProductPackageResponseDTO
    {
        $query = ['page_size' => $pageSize];
        if ($pageToken !== null && $pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $body = $productIds === [] ? [] : ['product_ids' => array_values($productIds)];

        $response = $this->makeRequest(HttpMethod::POST, '/product/202602/packages/recommend', $query, $body);

        return RecommendedProductPackageResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Pesquisa produtos na base do TikTok (inclusive de OUTROS vendedores) pra
     * fins de compliance. Nao devolve preco nem estoque.
     *
     * @param  array<string, mixed>  $filters  product_title, product_ids, seller_id, category_ids, brand_ids, seller_name
     */
    public function auditingResearch(array $filters = [], int $pageSize = 20, ?string $pageToken = null): ProductAuditingResearchResponseDTO
    {
        $query = ['page_size' => $pageSize];
        if ($pageToken !== null && $pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/product/202601/compliance/auditing/research', $query, $filters);

        return ProductAuditingResearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Contesta uma reprovacao de informacao do produto (hoje so' size chart).
     * Nao devolve corpo util — `data` vem vazio; sucesso = nao lancar excecao.
     *
     * @param  array<string, mixed>  $appealMessage  field_name, indicator_details[], size_chart_id, size_chart_image_uri
     * @return array<string, mixed>
     */
    public function submitAppeal(string $productId, array $appealMessage): array
    {
        return $this->makeRequest(HttpMethod::POST, '/product/202604/appeals/submit', [], [
            // A doc declara product_id como int; mandamos o valor cru pra nao
            // arriscar perda de precisao de snowflake em cast desnecessario.
            'product_id' => $productId,
            'appeal_message' => $appealMessage,
        ]);
    }

    // ---------------------------------------------------------------------
    // SKPP (Shop Key Product Program)
    // ---------------------------------------------------------------------

    /**
     * Score SKPP de um produto, com as tarefas que faltam e as recompensas.
     * `updateTime` e' snapshot offline (T+1/T+2), nao "agora".
     */
    public function getSkppDetail(string $productId, ?string $locale = null): ProductSkppDetailResponseDTO
    {
        $query = $locale === null ? [] : ['locale' => $locale];

        $response = $this->makeRequest(HttpMethod::GET, "/product/202606/skpps/{$productId}", $query);

        return ProductSkppDetailResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Status SKPP de varios produtos. Paginacao por page_no/page_size — NAO por
     * page_token, ao contrario do resto do grupo Product.
     *
     * @param  list<string>  $productIds  max 100
     */
    public function listSkppStatus(array $productIds = [], ?string $skppStatus = null, int $pageSize = 20, int $pageNo = 1): ProductSkppStatusListResponseDTO
    {
        $body = ['page_size' => $pageSize, 'page_no' => $pageNo];
        if ($productIds !== []) {
            $body['product_ids'] = array_values($productIds);
        }
        if ($skppStatus !== null) {
            $body['skpp_status'] = $skppStatus;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/product/202606/skpps/search', [], $body);

        return ProductSkppStatusListResponseDTO::fromArray($response['data'] ?? []);
    }

    /** Resumo SKPP da loja (quantos produtos qualificados, credito de anuncio ganho). */
    public function getShopSkppSummary(): ShopSkppSummaryResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/product/202606/skpps/sum');

        return ShopSkppSummaryResponseDTO::fromArray($response['data'] ?? []);
    }

    // ---------------------------------------------------------------------
    // Oportunidades de catalogo
    // ---------------------------------------------------------------------

    /**
     * Lista oportunidades (leads de produto/palavra/categoria).
     *
     * QUIRK: a resposta embrulha em `data.data` — desembrulhamos as duas camadas.
     *
     * @param  array<string, mixed>  $filters  category_ids, create_time_ge, create_time_lt, tag_codes
     */
    public function listOpportunities(string $opportunityType, array $filters = [], int $pageSize = 20, ?string $pageToken = null, ?string $locale = null): OpportunityListResponseDTO
    {
        $query = ['page_size' => $pageSize];
        if ($pageToken !== null && $pageToken !== '') {
            $query['page_token'] = $pageToken;
        }
        if ($locale !== null) {
            $query['locale'] = $locale;
        }

        $body = array_merge($filters, ['opportunity_type' => $opportunityType]);

        $response = $this->makeRequest(HttpMethod::POST, '/product/202604/opportunities/query', $query, $body);

        return OpportunityListResponseDTO::fromArray($response['data']['data'] ?? []);
    }

    /** Detalhe de uma oportunidade — traz os dados de mercado/conteudo que a lista nao traz. */
    public function getOpportunity(string $opportunityId, ?string $locale = null): OpportunityDetailResponseDTO
    {
        $query = $locale === null ? [] : ['locale' => $locale];

        $response = $this->makeRequest(HttpMethod::GET, "/product/202604/opportunities/{$opportunityId}", $query);

        return OpportunityDetailResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Submete um produto (que precisa estar em ACTIVATE) para uma oportunidade.
     * Volta em PENDING_REVIEW.
     */
    public function submitProductToOpportunity(string $opportunityId, string $productId): OpportunitySubmitResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/product/202604/opportunities/{$opportunityId}/submit", [], [
            'product_id' => $productId,
        ]);

        return OpportunitySubmitResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Historico de submissoes. Filtros vao na QUERY (opportunity_id, product_id,
     * status, submit_time_ge, submit_time_lt).
     *
     * QUIRK: tambem embrulha em `data.data`.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getSubmissionRecords(array $filters = [], int $pageSize = 20, ?string $pageToken = null): SubmissionRecordsResponseDTO
    {
        $query = array_merge($filters, ['page_size' => $pageSize]);
        if ($pageToken !== null && $pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/product/202604/opportunities/submissions', $query);

        return SubmissionRecordsResponseDTO::fromArray($response['data']['data'] ?? []);
    }

    // =====================================================================
    // PRODUTO GLOBAL (catalogo cross-border) e cadastros de apoio.
    //
    // O produto GLOBAL nao vende: e' o molde. Publicar (publishGlobal) e' que
    // gera um produto LOCAL por mercado — os metodos acima (search/get/
    // updatePrice/updateStock) operam nesses locais, com IDs de outro espaco.
    //
    // Cada endpoint tem sua PROPRIA versao de path (202309, 202312, 202507,
    // 202509, 202604...). Nao reaproveite self::VERSION aqui.
    // =====================================================================

    /**
     * Cria um produto no catalogo global.
     *
     * Criar NAO publica: o produto nasce so' no catalogo global e nao vira
     * anuncio em mercado nenhum ate' `publishGlobalProduct`.
     */
    public function createGlobalProduct(array $payload): GlobalProductCreateResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/product/202309/global_products', [], $payload);

        return GlobalProductCreateResponseDTO::fromArray($response['data'] ?? []);
    }

    public function getGlobalProduct(string $globalProductId): GlobalProductResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/product/202309/global_products/{$globalProductId}");

        return GlobalProductResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Busca produtos globais. Igual ao search local: page_size/page_token vao
     * na QUERY, os filtros no body. Path e' 202312 (nao 202309).
     */
    public function searchGlobalProducts(array $filters = [], int $pageSize = 20, ?string $pageToken = null): GlobalProductSearchResponseDTO
    {
        $query = ['page_size' => $pageSize];
        if ($pageToken !== null && $pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/product/202312/global_products/search', $query, $filters);

        return GlobalProductSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Edicao COMPLETA: o payload substitui o produto: campo omitido e' campo
     * apagado. Pra mexer em um campo so', use `partialEditGlobalProduct`.
     */
    public function editGlobalProduct(string $globalProductId, array $payload): GlobalProductEditResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/product/202309/global_products/{$globalProductId}", [], $payload);

        return GlobalProductEditResponseDTO::fromArray($response['data'] ?? []);
    }

    /** Edicao PARCIAL (v202509): so' os campos enviados mudam. */
    public function partialEditGlobalProduct(string $globalProductId, array $payload): GlobalProductEditResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/product/202509/global_products/{$globalProductId}/partial_edit", [], $payload);

        return GlobalProductEditResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Publica o produto global nos mercados indicados, criando um produto
     * local em cada um.
     *
     * ARMADILHA: sucesso parcial. Percorra `publishResult` checando `status`
     * por mercado — a chamada volta code 0 mesmo com mercado em FAILED/DRAFT.
     */
    public function publishGlobalProduct(string $globalProductId, array $payload): GlobalProductPublishResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, "/product/202309/global_products/{$globalProductId}/publish", [], $payload);

        return GlobalProductPublishResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Apaga produtos globais em lote (maximo 20 por chamada). Verbo DELETE com
     * corpo — nao e' POST.
     *
     * ARMADILHA: sucesso parcial. `errors` traz SO' os que falharam; vazio e'
     * que significa "todos apagados".
     *
     * @param list<string> $globalProductIds
     */
    public function deleteGlobalProducts(array $globalProductIds): GlobalProductDeleteResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::DELETE, '/product/202309/global_products', [], [
            'global_product_ids' => array_values($globalProductIds),
        ]);

        return GlobalProductDeleteResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Atualiza o estoque global dos SKUs. Os itens usam `global_warehouse_id`
     * (armazem GLOBAL) — nao o `warehouse_id` do produto local de updateStock.
     *
     * Resposta e' `data: {}`; devolvemos o envelope cru porque nao ha' o que
     * tipar.
     */
    public function updateGlobalInventory(string $globalProductId, array $globalSkus): array
    {
        return $this->makeRequest(HttpMethod::POST, "/product/202309/global_products/{$globalProductId}/inventory/update", [], [
            'global_skus' => $globalSkus,
        ]);
    }

    /**
     * Arvore INTEIRA de categorias globais (nao pagina) — remonte a hierarquia
     * por `parentId`.
     *
     * @param array $query locale, keyword, category_version (US/EU/SEA exigem 'v2')
     */
    public function getGlobalCategories(array $query = []): GlobalCategoryListResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/product/202309/global_categories', $query);

        return GlobalCategoryListResponseDTO::fromArray($response['data'] ?? []);
    }

    /** Sugere categoria global a partir de titulo/descricao/imagens do produto. */
    public function recommendGlobalCategories(array $payload): GlobalCategoryRecommendResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/product/202309/global_categories/recommend', [], $payload);

        return GlobalCategoryRecommendResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Regras de listagem da loja (v202507): metodo de listagem permitido e
     * modo de alocacao de estoque entre mercados. Consulte ANTES de decidir
     * entre publicacao global e replicacao local.
     */
    public function getGlobalListingRules(): GlobalListingRulesResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/product/202507/global_listing_rules');

        return GlobalListingRulesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Dispara a migracao dos produtos da loja pra arvore de categorias nova
     * (v2). Assincrona: `data` volta vazio, o efeito aparece nos produtos.
     */
    public function createCategoryUpgradeTask(array $payload = []): array
    {
        return $this->makeRequest(HttpMethod::POST, '/product/202407/products/category_upgrade_task', [], $payload);
    }

    /**
     * Dispara a migracao pro modelo GPA (Global Product Attributes), v202604.
     * Tambem assincrona e sem corpo de resposta.
     */
    public function createGpaUpgradeTask(array $payload = []): array
    {
        return $this->makeRequest(HttpMethod::POST, '/product/202604/products/gpa_upgrade_task', [], $payload);
    }

    /**
     * Cria tarefas de traducao de imagem (v202505).
     *
     * Uma tarefa = 1 imagem para 1 idioma: 10 imagens x 2 idiomas = 20 tarefas,
     * que e' o TETO por chamada. E' assincrono — aqui so' voltam os ids; a
     * imagem traduzida sai em `getImageTranslationTasks`.
     *
     * @param list<array{image_uri: string, target_languages: list<string>}> $images
     */
    public function createImageTranslationTasks(array $images): ImageTranslationTaskCreateResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/product/202505/images/translation_tasks', [], [
            'images' => array_values($images),
        ]);

        return ImageTranslationTaskCreateResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Consulta tarefas de traducao (v202506). Maximo 20 ids por chamada; a API
     * espera os ids CONCATENADOS por virgula numa unica query string.
     *
     * @param list<string> $translationTaskIds
     */
    public function getImageTranslationTasks(array $translationTaskIds): ImageTranslationTaskListResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/product/202506/images/translation_tasks', [
            'translation_task_ids' => implode(',', $translationTaskIds),
        ]);

        return ImageTranslationTaskListResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Cadastra um fabricante (compliance GPSR da UE).
     *
     * E' cadastro de LOJA, nao de produto: guarde o id retornado e referencie
     * em `manufacturer_ids` ao criar/editar o produto.
     */
    public function createManufacturer(array $payload): ManufacturerCreateResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/product/202409/compliance/manufacturers', [], $payload);

        return ManufacturerCreateResponseDTO::fromArray($response['data'] ?? []);
    }

    /** Edicao parcial de fabricante: so' os campos enviados mudam. `data` volta vazio. */
    public function partialEditManufacturer(string $manufacturerId, array $payload): array
    {
        return $this->makeRequest(HttpMethod::POST, "/product/202409/compliance/manufacturers/{$manufacturerId}/partial_edit", [], $payload);
    }

    /** Busca fabricantes (v202501). page_size/page_token na QUERY, filtros no body. */
    public function searchManufacturers(array $filters = [], int $pageSize = 20, ?string $pageToken = null): ManufacturerSearchResponseDTO
    {
        $query = ['page_size' => $pageSize];
        if ($pageToken !== null && $pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/product/202501/compliance/manufacturers/search', $query, $filters);

        return ManufacturerSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Cadastra uma pessoa responsavel (compliance GPSR da UE) — o contato
     * exigido dentro da UE pra produto importado. Referencie o id em
     * `responsible_person_ids` do produto.
     */
    public function createResponsiblePerson(array $payload): ResponsiblePersonCreateResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/product/202409/compliance/responsible_persons', [], $payload);

        return ResponsiblePersonCreateResponseDTO::fromArray($response['data'] ?? []);
    }

    /** Edicao parcial de pessoa responsavel. `data` volta vazio. */
    public function partialEditResponsiblePerson(string $responsiblePersonId, array $payload): array
    {
        return $this->makeRequest(HttpMethod::POST, "/product/202409/compliance/responsible_persons/{$responsiblePersonId}/partial_edit", [], $payload);
    }

    /** Busca pessoas responsaveis (v202501). page_size/page_token na QUERY. */
    public function searchResponsiblePersons(array $filters = [], int $pageSize = 20, ?string $pageToken = null): ResponsiblePersonSearchResponseDTO
    {
        $query = ['page_size' => $pageSize];
        if ($pageToken !== null && $pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/product/202501/compliance/responsible_persons/search', $query, $filters);

        return ResponsiblePersonSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Sobe uma imagem de produto e devolve a `uri` — que e' o que se referencia
     * em `main_images`/`sku_img`/descricao (a `url` e' so' pra exibir).
     *
     * Recebe o CONTEUDO do arquivo, nao o caminho: quem chama decide de onde
     * veio (disco, S3, stream) e o SDK nao toca no filesystem.
     *
     * `useCase`: MAIN_IMAGE, ATTRIBUTE_IMAGE, DESCRIPTION_IMAGE,
     * CERTIFICATION_IMAGE, SIZE_CHART_IMAGE, CUSTOMIZATION_IMAGE.
     */
    public function uploadProductImage(string $fileContents, string $fileName = 'image.jpg', ?string $useCase = null): UploadedProductImageResponseDTO
    {
        $fields = $useCase !== null ? ['use_case' => $useCase] : [];
        $response = $this->uploadMultipart('/product/202309/images/upload', $fileContents, $fileName, $fields);

        return UploadedProductImageResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Sobe um arquivo de produto (PDF de certificacao ou video) e devolve o
     * `id` — arquivo e' identificado por ID, imagem por URI. O `name` e'
     * obrigatorio pela API e precisa trazer a extensao (ex.: `certificado.pdf`),
     * sem espacos nem pontos extras.
     */
    public function uploadProductFile(string $fileContents, string $fileName): UploadedProductFileResponseDTO
    {
        $response = $this->uploadMultipart('/product/202309/files/upload', $fileContents, $fileName, [
            'name' => $fileName,
        ]);

        return UploadedProductFileResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * POST multipart assinado. O `makeRequest` do BaseMethods serializa o corpo
     * como JSON e o inclui na assinatura — nao serve pra upload. Aqui o corpo
     * fica FORA da assinatura (so' a query e' assinada), que e' o que o TikTok
     * espera em multipart/form-data.
     *
     * Usa um cliente PROPRIO (HttpClientFactory::makeMultipart) em vez do
     * `$this->httpClient`: aquele fixa `Content-Type: application/json`, e o
     * Guzzle so' gera o boundary do multipart quando o header nao vem definido
     * — reaproveita-lo mandaria corpo multipart com header de JSON.
     */
    private function uploadMultipart(string $apiPath, string $fileContents, string $fileName, array $fields = []): array
    {
        $apiPath = $this->normalizeApiPath($apiPath);
        $query = $this->buildSignedQuery($apiPath, [], null);

        $response = HttpClientFactory::makeMultipart($this->integration)
            ->attach('data', $fileContents, $fileName)
            ->post($apiPath.'?'.http_build_query($query), $fields);

        $data = $response->json() ?? [];
        if ($response->failed() || ($data['code'] ?? 0) !== 0) {
            $this->handleError($response);
        }

        return $data;
    }

    /**
     * Atributos built-in da categoria GLOBAL
     * (`GlobalCategoryAttributesResponseDTO`).
     *
     * Irmao global do `CategoryMethods::getAttributes()`: mesma categoria, mas
     * cada atributo vem com a matriz de obrigatoriedade POR MERCADO
     * (`requiredRegions` / `optionalRegions` / `requirementConditions`), que a
     * versao local nao tem.
     *
     * A categoria precisa ser FOLHA (a API recusa categoria intermediaria com
     * 12052024).
     *
     * @param  array<string, mixed>  $query  locale, category_version ('v2' em US/EU/SEA)
     */
    public function getGlobalAttributes(string $categoryId, array $query = []): GlobalCategoryAttributesResponseDTO
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/product/202309/categories/{$categoryId}/global_attributes",
            $query,
        );

        return GlobalCategoryAttributesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Regras da categoria GLOBAL (`GlobalCategoryRulesResponseDTO`):
     * certificacoes exigidas, suporte a tabela de medidas e os dois papeis do
     * GPSR europeu (responsavel e fabricante).
     *
     * Bloco AUSENTE quer dizer "a categoria nao exige" — nao confundir com
     * `is_required=false`, que significa "exigido apenas em alguns mercados,
     * veja as listas de regiao".
     *
     * @param  array<string, mixed>  $query  locale, category_version ('v2' em US/EU/SEA)
     */
    public function getGlobalCategoryRules(string $categoryId, array $query = []): GlobalCategoryRulesResponseDTO
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/product/202309/categories/{$categoryId}/global_rules",
            $query,
        );

        return GlobalCategoryRulesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Replicas do produto LOCAL nos outros mercados
     * (`GlobalReplicatedProductsResponseDTO`).
     *
     * O `$productId` e' o do produto LOCAL (path `/products/`, escopo
     * `seller.product.basic`) apesar do nome "global" — nao passe id de produto
     * global aqui.
     *
     * So' devolve algo em loja no modo LOCAL_REPLICATION; em GLOBAL_PUBLISHING
     * as publicacoes vivem no produto global. Ver `getGlobalListingRules()`.
     */
    public function getGlobalReplicatedProducts(string $productId): GlobalReplicatedProductsResponseDTO
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/product/202507/products/{$productId}/replicated_products",
        );

        return GlobalReplicatedProductsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Replica um produto LOCAL para outros mercados
     * (`GlobalReplicateProductResponseDTO`).
     *
     * ARMADILHA: e' lote e a resposta volta `code: 0` mesmo quando nenhum
     * mercado entrou — a falha por mercado esta' em `->errors`. Nunca dar a
     * replicacao por feita sem inspecionar.
     *
     * Todos os SKUs da origem precisam ir em cada alvo (a API recusa lista
     * parcial com 12052518) e `sale_price` e' STRING.
     *
     * `$inventoryMode` de TOPO e o de cada alvo existem os dois na doc, com a
     * mesma semantica (SHARED | EXCLUSIVE, default EXCLUSIVE, so' vale na UE):
     * o de topo e' o padrao, o do alvo sobrescreve. Em SHARED o estoque tem de
     * ser identico entre todos os SKUs interligados, senao vem 12052503.
     *
     * @param  list<array<string, mixed>>  $replicateTarget  itens {region, skus[], inventory_mode?}
     */
    public function replicateProduct(
        string $productId,
        array $replicateTarget,
        ?string $inventoryMode = null,
    ): GlobalReplicateProductResponseDTO {
        $body = array_filter([
            'replicate_target' => array_values($replicateTarget),
            'inventory_mode' => $inventoryMode,
        ], static fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/product/202507/products/{$productId}/global_replicate",
            [],
            $body,
        );

        return GlobalReplicateProductResponseDTO::fromArray($response['data'] ?? []);
    }
}
