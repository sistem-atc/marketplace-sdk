<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Product;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Product\ItemBaseInfoResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Product\ItemListResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Product\ModelListResponseDTO;

class ProductMethods extends BaseMethods
{
    /** Paginacao por OFFSET (nao cursor): pagine enquanto ->hasNextPage. */
    public function getItemList(int $offset = 0, int $pageSize = 20, array $filters = []): ItemListResponseDTO
    {
        $query = array_merge([
            'offset' => $offset,
            'page_size' => $pageSize,
            'item_status' => 'NORMAL',
        ], $filters);

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_item_list', $query);

        return ItemListResponseDTO::fromArray($response['response'] ?? []);
    }

    public function getItemBaseInfo(array $itemIds): ItemBaseInfoResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_item_base_info', [
            'item_id_list' => implode(',', $itemIds),
        ]);

        return ItemBaseInfoResponseDTO::fromArray($response['response'] ?? []);
    }

    /**
     * Variacoes (models) de um anuncio com `has_model: true`. O preco de itens com
     * variacao NAO vem no item (price_info null) — vem por model aqui.
     *
     * GET /api/v2/product/get_model_list?item_id={id}
     *
     * Retorna response.model[] cada um com model_id, model_sku, tier_index[] e
     * price_info[{current_price, original_price}]; + response.tier_variation[] (os
     * nomes/opcoes das variacoes, ex.: "Peso" => 250g/500g/1Kg).
     */
    public function getModelList(int $itemId): ModelListResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_model_list', [
            'item_id' => $itemId,
        ]);

        return ModelListResponseDTO::fromArray($response['response'] ?? []);
    }

    public function updateStock(int $itemId, int $stock, ?int $variationId = null): array
    {
        $body = [
            'item_id' => $itemId,
            'stock_list' => [
                ['normal_stock' => $stock]
            ]
        ];
        if ($variationId) {
            $body['stock_list'][0]['model_id'] = $variationId;
        }

        return $this->makeRequest(HttpMethod::POST, '/api/v2/product/update_stock', [], $body);
    }

    public function updatePrice(int $itemId, float $price, ?int $variationId = null): array
    {
        $body = [
            'item_id' => $itemId,
            'price_list' => [
                ['original_price' => $price]
            ]
        ];
        if ($variationId) {
            $body['price_list'][0]['model_id'] = $variationId;
        }

        return $this->makeRequest(HttpMethod::POST, '/api/v2/product/update_price', [], $body);
    }

    // -----------------------------------------------------------------------
    // Leitura / busca de itens
    // -----------------------------------------------------------------------

    /**
     * Busca de itens por nome/SKU/status (diferente do get_item_list, que lista
     * por status e periodo). Paginacao por OFFSET em STRING: repasse o
     * `next_offset` da resposta enquanto `has_next_page` for true.
     *
     * GET /api/v2/product/search_item
     *
     * @param  array<string, mixed>  $filters  item_name, item_sku, attribute_status (1=falta obrigatorio, 2=falta opcional), item_status (lista: NORMAL/BANNED/UNLIST/REVIEWING/SELLER_DELETE/SHOPEE_DELETE), deboost_only (bool)
     * @return array<string, mixed> response{item_id_list[], has_next_page, next_offset, total_count}
     */
    public function searchItem(int $pageSize = 100, string $offset = '', array $filters = []): array
    {
        if (isset($filters['item_status']) && is_array($filters['item_status'])) {
            $filters['item_status'] = implode(',', $filters['item_status']);
        }
        if (isset($filters['deboost_only'])) {
            $filters['deboost_only'] = $filters['deboost_only'] ? 'true' : 'false';
        }

        $query = array_merge(['page_size' => $pageSize], $filters);
        if ($offset !== '') {
            $query['offset'] = $offset;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/search_item', $query);

        return $response['response'] ?? [];
    }

    /**
     * Metricas do anuncio (vendas, visualizacoes, likes, avaliacao, comentarios).
     * Limite de 50 item_id por chamada.
     *
     * GET /api/v2/product/get_item_extra_info
     *
     * @param  array<int, int>  $itemIds
     * @return array<int, array<string, mixed>> response.item_list[]
     */
    public function getItemExtraInfo(array $itemIds): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_item_extra_info', [
            'item_id_list' => implode(',', $itemIds),
        ]);

        return $response['response']['item_list'] ?? [];
    }

    /**
     * Promocoes ativas/futuras em cada item (1 a 50 ids).
     *
     * GET /api/v2/product/get_item_promotion
     *
     * @param  array<int, int>  $itemIds
     * @return array<string, mixed> response{success_list[], failure_list[]}
     */
    public function getItemPromotion(array $itemIds): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_item_promotion', [
            'item_id_list' => implode(',', $itemIds),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Violacoes/penalidades de anuncios (BANNED, deboost etc.). Ate 50 ids.
     *
     * GET /api/v2/product/get_item_violation_info
     *
     * @param  array<int, int>  $itemIds
     * @return array<string, mixed>
     */
    public function getItemViolationInfo(array $itemIds): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_item_violation_info', [
            'item_id_list' => implode(',', $itemIds),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Limites do cadastro por categoria (tamanho de nome/descricao, nº de
     * imagens, faixa de preco, peso, DTS...). Sem category_id devolve o limite
     * geral da loja.
     *
     * GET /api/v2/product/get_item_limit
     *
     * @return array<string, mixed>
     */
    public function getItemLimit(?int $categoryId = null): array
    {
        $query = $categoryId !== null ? ['category_id' => $categoryId] : [];

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_item_limit', $query);

        return $response['response'] ?? [];
    }

    // -----------------------------------------------------------------------
    // Cadastro / alteracao de item
    // -----------------------------------------------------------------------

    /**
     * Cria um anuncio. Obrigatorios: original_price, description, weight (kg),
     * item_name, logistic_info[], category_id, image{image_id_list[]}. As
     * imagens vem do media_space/upload_image (image_id). Atributos
     * obrigatorios da categoria (CategoryMethods::getAttributes) sao validados
     * pela Shopee. No Brasil `tax_info` (ncm, cfop, csosn/icms_cst, origin...)
     * e' exigido pra emitir nota.
     *
     * POST /api/v2/product/add_item
     *
     * @param  array<string, mixed>  $payload  corpo completo conforme catalogo
     * @return array<string, mixed> response com item_id e o item criado
     */
    public function addItem(array $payload): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/add_item', [], $payload);

        return $response['response'] ?? [];
    }

    /**
     * Altera campos de um anuncio. Campos omitidos sao mantidos; listas
     * (wholesale, video_upload_id) enviadas vazias APAGAM. Preco/estoque NAO
     * passam aqui (usar updatePrice/updateStock).
     *
     * POST /api/v2/product/update_item
     *
     * @param  array<string, mixed>  $payload  campos a alterar (sem item_id)
     * @return array<string, mixed>
     */
    public function updateItem(int $itemId, array $payload): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/update_item', [], array_merge(
            ['item_id' => $itemId],
            $payload,
        ));

        return $response['response'] ?? [];
    }

    /**
     * Exclui um anuncio (irreversivel — prefira unlistItems).
     *
     * POST /api/v2/product/delete_item
     *
     * @return array<string, mixed> envelope completo (request_id, warning)
     */
    public function deleteItem(int $itemId): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/product/delete_item', [], [
            'item_id' => $itemId,
        ]);
    }

    /**
     * Pausa (unlist=true) ou reativa (unlist=false) de 1 a 50 anuncios.
     *
     * POST /api/v2/product/unlist_item
     *
     * @param  array<int, int>  $itemIds
     * @return array<string, mixed> response{success_list[], failure_list[]}
     */
    public function unlistItems(array $itemIds, bool $unlist = true): array
    {
        $itemList = array_map(
            static fn (int $id): array => ['item_id' => $id, 'unlist' => $unlist],
            array_values($itemIds),
        );

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/unlist_item', [], [
            'item_list' => $itemList,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Impulsiona (boost gratuito) 1 a 5 itens por chamada; a Shopee limita a
     * 5 itens impulsionados por vez, 4h cada.
     *
     * POST /api/v2/product/boost_item
     *
     * @param  array<int, int>  $itemIds
     * @return array<string, mixed> response{success_list[], failure_list[]}
     */
    public function boostItems(array $itemIds): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/boost_item', [], [
            'item_id_list' => array_values($itemIds),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Itens atualmente impulsionados (com cool_down_second).
     *
     * GET /api/v2/product/get_boosted_list
     *
     * @return array<int, array<string, mixed>> response.item_list[]
     */
    public function getBoostedList(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_boosted_list');

        return $response['response']['item_list'] ?? [];
    }

    // -----------------------------------------------------------------------
    // Variacoes (tier variation / models)
    // -----------------------------------------------------------------------

    /**
     * Transforma um item SEM variacao em item COM variacao: define os niveis
     * (standardise_tier_variation, ate 2 niveis) e cria os models (ate 50).
     * Cada model: tier_index[], original_price, model_sku, seller_stock[].
     *
     * POST /api/v2/product/init_tier_variation
     *
     * @param  array<int, array<string, mixed>>  $models
     * @param  array<int, array<string, mixed>>  $tierVariations
     * @return array<string, mixed> response{tier_variation[], model[]}
     */
    public function initTierVariation(int $itemId, array $models, array $tierVariations = []): array
    {
        $body = ['item_id' => $itemId, 'model' => array_values($models)];
        if ($tierVariations !== []) {
            $body['standardise_tier_variation'] = array_values($tierVariations);
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/init_tier_variation', [], $body);

        return $response['response'] ?? [];
    }

    /**
     * Renomeia niveis/opcoes de variacao ou reindexa models (model_list com
     * model_id + tier_index[]). Nao cria nem apaga model — usar add/deleteModel.
     *
     * POST /api/v2/product/update_tier_variation
     *
     * @param  array<int, array<string, mixed>>  $tierVariations
     * @param  array<int, array<string, mixed>>  $modelList
     * @return array<string, mixed> envelope completo
     */
    public function updateTierVariation(int $itemId, array $tierVariations = [], array $modelList = []): array
    {
        $body = ['item_id' => $itemId];
        if ($tierVariations !== []) {
            $body['standardise_tier_variation'] = array_values($tierVariations);
        }
        if ($modelList !== []) {
            $body['model_list'] = array_values($modelList);
        }

        return $this->makeRequest(HttpMethod::POST, '/api/v2/product/update_tier_variation', [], $body);
    }

    /**
     * Adiciona models a um item que ja' tem variacao. Cada model: tier_index[],
     * original_price, model_sku, seller_stock[], gtin_code, weight, dimension.
     *
     * POST /api/v2/product/add_model
     *
     * @param  array<int, array<string, mixed>>  $models
     * @return array<int, array<string, mixed>> response.model[]
     */
    public function addModels(int $itemId, array $models): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/add_model', [], [
            'item_id' => $itemId,
            'model_list' => array_values($models),
        ]);

        return $response['response']['model'] ?? [];
    }

    /**
     * Altera SKU/GTIN/peso/dimensao/pre-order/status de 1 a 50 models. Preco e
     * estoque NAO passam aqui (updatePrice/updateStock com model_id).
     *
     * POST /api/v2/product/update_model
     *
     * @param  array<int, array<string, mixed>>  $models  cada um com model_id
     * @return array<string, mixed> envelope completo
     */
    public function updateModels(int $itemId, array $models): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/product/update_model', [], [
            'item_id' => $itemId,
            'model' => array_values($models),
        ]);
    }

    /**
     * Remove um model. Item com 1 unico model nao aceita.
     *
     * POST /api/v2/product/delete_model
     *
     * @return array<string, mixed> envelope completo
     */
    public function deleteModel(int $itemId, int $modelId): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/product/delete_model', [], [
            'item_id' => $itemId,
            'model_id' => $modelId,
        ]);
    }

    /**
     * Preco por model de item SIP (Shopee International Platform).
     *
     * POST /api/v2/product/update_sip_item_price
     *
     * @param  array<int, array{model_id?: int, sip_item_price: float}>  $prices
     * @return array<string, mixed> envelope completo
     */
    public function updateSipItemPrice(int $itemId, array $prices): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/product/update_sip_item_price', [], [
            'item_id' => $itemId,
            'sip_item_price' => array_values($prices),
        ]);
    }

    /**
     * Itens afiliados (A-item) gerados nas lojas SIP a partir de um item da
     * loja primaria (P-item).
     *
     * GET /api/v2/product/get_aitem_by_pitem_id
     *
     * @return array<int, array<string, mixed>> response.aitem_list[]
     */
    public function getAitemByPitemId(int $pitemId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_aitem_by_pitem_id', [
            'pitem_id' => $pitemId,
        ]);

        return $response['response']['aitem_list'] ?? [];
    }

    // -----------------------------------------------------------------------
    // Comentarios / avaliacoes
    // -----------------------------------------------------------------------

    /**
     * Avaliacoes de produto. Paginacao por CURSOR (`next_cursor` enquanto
     * `more`). Filtros opcionais por item ou comentario.
     *
     * GET /api/v2/product/get_comment
     *
     * @return array<string, mixed> response{item_comment_list[], more, next_cursor}
     */
    public function getComments(int $pageSize = 100, string $cursor = '', ?int $itemId = null, ?int $commentId = null): array
    {
        $query = ['cursor' => $cursor, 'page_size' => $pageSize];
        if ($itemId !== null) {
            $query['item_id'] = $itemId;
        }
        if ($commentId !== null) {
            $query['comment_id'] = $commentId;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_comment', $query);

        return $response['response'] ?? [];
    }

    /**
     * Responde de 1 a 100 avaliacoes. Cada resposta so' pode ser dada UMA vez
     * (editable=EDITABLE indica que ainda aceita).
     *
     * POST /api/v2/product/reply_comment
     *
     * @param  array<int, array{comment_id: int, comment: string}>  $replies
     * @return array<int, array<string, mixed>> response.result_list[]
     */
    public function replyComments(array $replies): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/reply_comment', [], [
            'comment_list' => array_values($replies),
        ]);

        return $response['response']['result_list'] ?? [];
    }

    // -----------------------------------------------------------------------
    // Recomendacoes / tabela de medidas / veiculos
    // -----------------------------------------------------------------------

    /**
     * Faixa de peso sugerida pela Shopee pra um cadastro (usa nome, capa,
     * categoria, atributos, marca e descricao).
     *
     * POST /api/v2/product/get_weight_recommendation
     *
     * @param  array<string, mixed>  $payload  item_name, cover_image_id, category_id, attribute_list[], brand_id, description_type (+ description | description_info)
     * @return array<string, mixed> response{normal_weight_range{...}}
     */
    public function getWeightRecommendation(array $payload): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/get_weight_recommendation', [], $payload);

        return $response['response'] ?? [];
    }

    /**
     * Tabelas de medidas da loja aplicaveis a uma categoria. Cursor; page_size max 50.
     *
     * GET /api/v2/product/get_size_chart_list
     *
     * @return array<string, mixed> response{size_chart_list[], next_cursor, total_count}
     */
    public function getSizeChartList(int $categoryId, int $pageSize = 50, string $cursor = ''): array
    {
        $query = ['category_id' => $categoryId, 'page_size' => $pageSize];
        if ($cursor !== '') {
            $query['cursor'] = $cursor;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_size_chart_list', $query);

        return $response['response'] ?? [];
    }

    /**
     * Detalhe (linhas/colunas) de uma tabela de medidas.
     *
     * GET /api/v2/product/get_size_chart_detail
     *
     * @return array<string, mixed> response{size_chart_id, size_chart_name, size_chart_table}
     */
    public function getSizeChartDetail(int $sizeChartId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_size_chart_detail', [
            'size_chart_id' => $sizeChartId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Catalogo de veiculos (compatibilidade de autopecas). Offset; page_size max 100.
     *
     * GET /api/v2/product/get_all_vehicle_list
     *
     * @return array<string, mixed> response{vehicle_list[], has_next_page, next_offset}
     */
    public function getAllVehicleList(int $pageSize = 100, int $offset = 0, ?string $language = null): array
    {
        $query = ['page_size' => $pageSize, 'offset' => $offset];
        if ($language !== null) {
            $query['language'] = $language;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_all_vehicle_list', $query);

        return $response['response'] ?? [];
    }

    /**
     * Veiculos filtrados por nivel (Brand, Model, Year ou Version) e ids do nivel superior.
     *
     * GET /api/v2/product/get_vehicle_list_by_compatibility_detail
     *
     * @param  array<string, mixed>  $filters  brand_id, model_id, year_id, language
     * @return array<int, array<string, mixed>> response.vehicle_list[]
     */
    public function getVehicleListByCompatibilityDetail(string $compatibilityDetails, array $filters = []): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_vehicle_list_by_compatibility_detail', array_merge(
            ['compatibility_details' => $compatibilityDetails],
            $filters,
        ));

        return $response['response']['vehicle_list'] ?? [];
    }

    // -----------------------------------------------------------------------
    // Diagnostico de conteudo
    // -----------------------------------------------------------------------

    /**
     * Qualidade de conteudo (quality_level + issues) de 1 a 48 itens.
     *
     * POST /api/v2/product/get_item_content_diagnosis_result
     *
     * @param  array<int, int>  $itemIds
     * @return array<string, mixed> response{success_item_list[], failure_item_list[]}
     */
    public function getItemContentDiagnosisResult(array $itemIds): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/get_item_content_diagnosis_result', [], [
            'item_id_list' => array_values($itemIds),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Lista itens por nivel de qualidade/tipo de problema. Offset string
     * (`next_offset`), page_size max 48.
     *
     * POST /api/v2/product/get_item_list_by_content_diagnosis
     *
     * @param  array<int, int>  $qualityLevels  1=TO_BE_IMPROVED 2=QUALIFIED 3=GOOD 4=EXCELLENT
     * @param  array<int, int>  $issueTypes
     * @return array<string, mixed> response{item_list[], total_count, next_offset}
     */
    public function getItemListByContentDiagnosis(int $pageSize = 48, string $offset = '', array $qualityLevels = [], array $issueTypes = []): array
    {
        $body = ['page_size' => $pageSize];
        if ($offset !== '') {
            $body['offset'] = $offset;
        }
        if ($qualityLevels !== []) {
            $body['quality_level'] = array_values($qualityLevels);
        }
        if ($issueTypes !== []) {
            $body['issue_type'] = array_values($issueTypes);
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/get_item_list_by_content_diagnosis', [], $body);

        return $response['response'] ?? [];
    }

    // -----------------------------------------------------------------------
    // Kits (combos)
    // -----------------------------------------------------------------------

    /**
     * Limites do cadastro de KIT (nº de componentes por model, preco, imagens...).
     *
     * GET /api/v2/product/get_kit_item_limit
     *
     * @return array<string, mixed>
     */
    public function getKitItemLimit(?int $categoryId = null): array
    {
        $query = $categoryId !== null ? ['category_id' => $categoryId] : [];

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_kit_item_limit', $query);

        return $response['response'] ?? [];
    }

    /**
     * Cria um item KIT (combo de componentes existentes). item_setting: item_name,
     * images, description, logistic_info[], model_list[] (cada model com
     * component_list); sync_setting.auto_sync_dts.
     *
     * POST /api/v2/product/add_kit_item
     *
     * @param  array<string, mixed>  $itemSetting
     * @param  array<string, mixed>  $syncSetting
     * @return array<string, mixed> response{item_id}
     */
    public function addKitItem(array $itemSetting, array $syncSetting = []): array
    {
        $body = ['item_setting' => $itemSetting];
        if ($syncSetting !== []) {
            $body['sync_setting'] = $syncSetting;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/add_kit_item', [], $body);

        return $response['response'] ?? [];
    }

    /**
     * Altera um KIT (mesmos blocos do addKitItem).
     *
     * POST /api/v2/product/update_kit_item
     *
     * @param  array<string, mixed>  $itemSetting
     * @param  array<string, mixed>  $syncSetting
     * @return array<string, mixed> envelope completo
     */
    public function updateKitItem(int $itemId, array $itemSetting = [], array $syncSetting = []): array
    {
        $body = ['item_id' => $itemId];
        if ($itemSetting !== []) {
            $body['item_setting'] = $itemSetting;
        }
        if ($syncSetting !== []) {
            $body['sync_setting'] = $syncSetting;
        }

        return $this->makeRequest(HttpMethod::POST, '/api/v2/product/update_kit_item', [], $body);
    }

    /**
     * Detalhe de um KIT (componentes por model).
     *
     * GET /api/v2/product/get_kit_item_info
     *
     * @return array<string, mixed> response.product_info
     */
    public function getKitItemInfo(int $itemId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_kit_item_info', [
            'item_id' => $itemId,
        ]);

        return $response['response']['product_info'] ?? [];
    }

    /**
     * Gera a imagem composta de um KIT a partir de ate 9 componentes.
     *
     * POST /api/v2/product/generate_kit_image
     *
     * @param  array<int, array{component_item_id: int, component_model_id?: int}>  $components
     * @return array<string, mixed>
     */
    public function generateKitImage(array $components): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/generate_kit_image', [], [
            'component_list' => array_values($components),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Models "sem embalagem" (unpackaged SKU) — busca por item/model/sku. Cursor.
     *
     * POST /api/v2/product/search_unpackaged_model_list
     *
     * @param  array<string, mixed>  $filters  item_id, item_name, model_id, unpackaged_sku_id
     * @return array<string, mixed> response{model_list[], next_cursor, total_count}
     */
    public function searchUnpackagedModelList(int $pageSize = 100, string $cursor = '', array $filters = []): array
    {
        $body = array_merge(['page_size' => $pageSize], $filters);
        if ($cursor !== '') {
            $body['cursor'] = $cursor;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/search_unpackaged_model_list', [], $body);

        return $response['response'] ?? [];
    }

    // -----------------------------------------------------------------------
    // Direct shop (loja principal x lojas diretas)
    // -----------------------------------------------------------------------

    /**
     * Item da loja principal correspondente a itens de loja direta.
     *
     * GET /api/v2/product/get_main_item_list
     *
     * @param  array<int, int>  $directItemIds
     * @return array<int, array<string, mixed>> response.list[]
     */
    public function getMainItemList(array $directItemIds): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_main_item_list', [
            'direct_item_id' => implode(',', $directItemIds),
        ]);

        return $response['response']['list'] ?? [];
    }

    /**
     * Itens de loja direta gerados a partir de itens da loja principal.
     *
     * GET /api/v2/product/get_direct_item_list
     *
     * @param  array<int, int>  $mainItemIds
     * @return array<int, array<string, mixed>> response.list[]
     */
    public function getDirectItemList(array $mainItemIds): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_direct_item_list', [
            'main_item_id' => implode(',', $mainItemIds),
        ]);

        return $response['response']['list'] ?? [];
    }

    /**
     * Preco recomendado pra lojas diretas (por regiao) a partir do item principal.
     *
     * GET /api/v2/product/get_direct_shop_recommended_price
     *
     * @param  array<int, string>  $directShopRegions
     * @param  array<string, mixed>  $extra  category_id, model_list[], enabled_channel_id_list[]
     * @return array<string, mixed> response{direct_item_price[]}
     */
    public function getDirectShopRecommendedPrice(int $mainItemId, array $directShopRegions, array $extra = []): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_direct_shop_recommended_price', array_merge([
            'main_item_id' => $mainItemId,
            'direct_shop_regions' => implode(',', $directShopRegions),
        ], $extra));

        return $response['response'] ?? [];
    }

    // -----------------------------------------------------------------------
    // Mart / Outlet (loja-mae x lojas outlet) — operacoes em lote assincronas
    // -----------------------------------------------------------------------

    /**
     * Mapeamento de um item da loja Mart nas lojas outlet informadas.
     *
     * POST /api/v2/product/get_mart_item_mapping_by_id
     *
     * @param  array<int, int>  $outletShopIds
     * @return array<string, mixed>
     */
    public function getMartItemMappingById(int $martItemId, array $outletShopIds): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/get_mart_item_mapping_by_id', [], [
            'mart_item_id' => $martItemId,
            'outlet_shop_id_list' => array_values($outletShopIds),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Item Mart de origem de um item de loja outlet.
     *
     * POST /api/v2/product/get_mart_item_by_outlet_item_id
     *
     * @return array<string, mixed>
     */
    public function getMartItemByOutletItemId(int $outletItemId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/get_mart_item_by_outlet_item_id', [], [
            'outlet_item_id' => $outletItemId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Atualiza preco em lojas outlet em LOTE (1 a 100 itens). Assincrono:
     * devolve task_id -> consultar em getBatchTaskResult(taskType: 1).
     *
     * POST /api/v2/product/batch_update_outlet_price
     *
     * @param  array<int, array{outlet_shop_id: int, item_id: int, price_list: array<int, array<string, mixed>>}>  $items
     * @return array<string, mixed> response{task_id}
     */
    public function batchUpdateOutletPrice(array $items): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/batch_update_outlet_price', [], [
            'item_list' => array_values($items),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Atualiza estoque em lojas outlet em LOTE (1 a 100). task_type 2 no getBatchTaskResult.
     *
     * POST /api/v2/product/batch_update_outlet_stock
     *
     * @param  array<int, array{outlet_shop_id: int, item_id: int, stock_list: array<int, array<string, mixed>>}>  $items
     * @return array<string, mixed> response{task_id}
     */
    public function batchUpdateOutletStock(array $items): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/batch_update_outlet_stock', [], [
            'item_list' => array_values($items),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Publica itens Mart em lojas outlet em LOTE (1 a 100). task_type 3.
     *
     * POST /api/v2/product/batch_publish_item_to_outlet_shop
     *
     * @param  array<int, array{mart_item_id: int, outlet_shop_id: int, publish_item: array<string, mixed>}>  $items
     * @return array<string, mixed> response{task_id}
     */
    public function batchPublishItemToOutletShop(array $items): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/batch_publish_item_to_outlet_shop', [], [
            'item_list' => array_values($items),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Cria ate 100 anuncios em LOTE (mesmo payload do addItem por item). task_type 4.
     *
     * POST /api/v2/product/batch_add_item
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<string, mixed> response{task_id}
     */
    public function batchAddItems(array $items): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/batch_add_item', [], [
            'item_list' => array_values($items),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Resultado de uma tarefa em lote. task_type: 1=preco outlet, 2=estoque
     * outlet, 3=publicar outlet, 4=batch_add_item.
     *
     * GET /api/v2/product/get_batch_task_result
     *
     * @return array<string, mixed> response{publish_status, success_list[], failed_list[]}
     */
    public function getBatchTaskResult(int $taskType, int $taskId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_batch_task_result', [
            'task_type' => $taskType,
            'task_id' => $taskId,
        ]);

        return $response['response'] ?? [];
    }
}
