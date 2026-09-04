<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\GlobalProduct;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Global Product (api_type Merchant) — catalogo global do vendedor cross-border
 * (CNSC/KRSC) que e' publicado nas lojas locais.
 *
 * TODAS as chamadas assinam com merchant_id (settings['merchant_id']) no lugar
 * de shop_id e exigem access_token autorizado pelo MERCHANT (o token de loja
 * nao serve). Retorno bruto `array` (envelope completo: error/message/
 * warning/request_id/response).
 */
class GlobalProductMethods extends BaseMethods
{
    /** @param array<string,mixed> $query */
    private function get(string $name, array $query = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/global_product/'.$name, $query, [], false, 0, true);
    }

    /** @param array<string,mixed> $body */
    private function post(string $name, array $body = []): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/global_product/'.$name, [], $body, false, 0, true);
    }

    // ---------------------------------------------------------------- catalogo

    /**
     * Lista global items por janela de update_time. Paginacao por cursor
     * (`offset` string; devolve response.next_offset / has_next_page).
     * page_size [1,50].
     *
     * @param array<string,mixed> $filters update_time_from / update_time_to (unix)
     */
    public function getGlobalItemList(?string $offset = null, int $pageSize = 50, array $filters = []): array
    {
        $query = array_merge(['page_size' => $pageSize], $filters);
        if ($offset !== null) {
            $query['offset'] = $offset;
        }

        return $this->get('get_global_item_list', $query);
    }

    /** Detalhe de ate' 20 global items por chamada. @param int[] $globalItemIds */
    public function getGlobalItemInfo(array $globalItemIds): array
    {
        return $this->get('get_global_item_info', [
            'global_item_id_list' => implode(',', $globalItemIds),
        ]);
    }

    /** Mapeia item_id de UMA loja -> global_item_id (ate' 20 por chamada). @param int[] $itemIds */
    public function getGlobalItemId(int $shopId, array $itemIds): array
    {
        return $this->get('get_global_item_id', [
            'shop_id' => $shopId,
            'item_id_list' => implode(',', $itemIds),
        ]);
    }

    /** Limites (nome, descricao, imagens, preco…) da categoria pra criar global item. */
    public function getGlobalItemLimit(int $categoryId): array
    {
        return $this->get('get_global_item_limit', ['category_id' => $categoryId]);
    }

    /** @param array<string,mixed> $data category_id, global_item_name, description, original_price, normal_stock, weight, image… */
    public function addGlobalItem(array $data): array
    {
        return $this->post('add_global_item', $data);
    }

    /** @param array<string,mixed> $data campos a alterar (so' o que for enviado muda) */
    public function updateGlobalItem(int $globalItemId, array $data): array
    {
        return $this->post('update_global_item', array_merge(['global_item_id' => $globalItemId], $data));
    }

    public function deleteGlobalItem(int $globalItemId): array
    {
        return $this->post('delete_global_item', ['global_item_id' => $globalItemId]);
    }

    // ------------------------------------------------------------------ models

    public function getGlobalModelList(int $globalItemId): array
    {
        return $this->get('get_global_model_list', ['global_item_id' => $globalItemId]);
    }

    /**
     * Cria a estrutura de variacao (tier) do global item, com os models
     * iniciais (ate' 50). Precisa de standardise_tier_variation.
     *
     * @param array<int,array<string,mixed>> $globalModel
     * @param array<int,array<string,mixed>> $standardiseTierVariation
     */
    public function initTierVariation(int $globalItemId, array $globalModel, array $standardiseTierVariation): array
    {
        return $this->post('init_tier_variation', [
            'global_item_id' => $globalItemId,
            'global_model' => array_values($globalModel),
            'standardise_tier_variation' => array_values($standardiseTierVariation),
        ]);
    }

    /**
     * @param array<int,array<string,mixed>> $standardiseTierVariation
     * @param array<int,array{model_id:int,tier_index:array<int>}> $modelList
     */
    public function updateTierVariation(int $globalItemId, array $standardiseTierVariation, array $modelList = []): array
    {
        $body = [
            'global_item_id' => $globalItemId,
            'standardise_tier_variation' => array_values($standardiseTierVariation),
        ];
        if ($modelList !== []) {
            $body['model_list'] = array_values($modelList);
        }

        return $this->post('update_tier_variation', $body);
    }

    /** Ate' 50 models por chamada. @param array<int,array<string,mixed>> $globalModel */
    public function addGlobalModel(int $globalItemId, array $globalModel): array
    {
        return $this->post('add_global_model', [
            'global_item_id' => $globalItemId,
            'global_model' => array_values($globalModel),
        ]);
    }

    /** Ate' 50 models por chamada. @param array<int,array<string,mixed>> $globalModel */
    public function updateGlobalModel(int $globalItemId, array $globalModel): array
    {
        return $this->post('update_global_model', [
            'global_item_id' => $globalItemId,
            'global_model' => array_values($globalModel),
        ]);
    }

    public function deleteGlobalModel(int $globalItemId, int $globalModelId): array
    {
        return $this->post('delete_global_model', [
            'global_item_id' => $globalItemId,
            'global_model_id' => $globalModelId,
        ]);
    }

    // ------------------------------------------------------------- preco/estoque

    /** Ate' 50 models. @param array<int,array{global_model_id:int,original_price:float}> $priceList */
    public function updatePrice(int $globalItemId, array $priceList): array
    {
        return $this->post('update_price', [
            'global_item_id' => $globalItemId,
            'price_list' => array_values($priceList),
        ]);
    }

    /**
     * Ate' 50 models. Merchant com loja 3PF precisa informar location_id em
     * seller_stock (ver MerchantMethods::getMerchantWarehouseLocationList).
     *
     * @param array<int,array{global_model_id:int,seller_stock:array<int,array{location_id?:string,stock:int}>}> $stockList
     */
    public function updateStock(int $globalItemId, array $stockList): array
    {
        return $this->post('update_stock', [
            'global_item_id' => $globalItemId,
            'stock_list' => array_values($stockList),
        ]);
    }

    /** Fator que converte preco cross-border em preco local de UMA loja. */
    public function getLocalAdjustmentRate(int $shopId): array
    {
        return $this->get('get_local_adjustment_rate', ['shop_id' => $shopId]);
    }

    public function updateLocalAdjustmentRate(int $shopId, float $adjustmentRate): array
    {
        return $this->post('update_local_adjustment_rate', [
            'shop_id' => $shopId,
            'adjustment_rate' => $adjustmentRate,
        ]);
    }

    // ---------------------------------------------------------------- publish

    /**
     * Publica o global item numa loja local. Assincrono: devolve publish_task_id,
     * acompanhe com getPublishTaskResult().
     *
     * @param array<string,mixed> $item overrides locais (item_name, description, original_price, logistic…)
     */
    public function createPublishTask(int $globalItemId, int $shopId, string $shopRegion, array $item = []): array
    {
        $body = [
            'global_item_id' => $globalItemId,
            'shop_id' => $shopId,
            'shop_region' => $shopRegion,
        ];
        if ($item !== []) {
            $body['item'] = $item;
        }

        return $this->post('create_publish_task', $body);
    }

    public function getPublishTaskResult(int $publishTaskId): array
    {
        return $this->get('get_publish_task_result', ['publish_task_id' => $publishTaskId]);
    }

    /** Lojas onde o global item pode ser publicado (sem lista: primeiras 300). @param int[] $shopIds */
    public function getPublishableShop(int $globalItemId, array $shopIds = []): array
    {
        $query = ['global_item_id' => $globalItemId];
        if ($shopIds !== []) {
            $query['shop_id_list'] = implode(',', $shopIds);
        }

        return $this->get('get_publishable_shop', $query);
    }

    /** Lojas onde o global item JA' esta' publicado (item_id local por loja). @param int[] $shopIds */
    public function getPublishedList(int $globalItemId, array $shopIds = []): array
    {
        $query = ['global_item_id' => $globalItemId];
        if ($shopIds !== []) {
            $query['shop_id_list'] = implode(',', $shopIds);
        }

        return $this->get('get_published_list', $query);
    }

    /** Status de publicabilidade por loja, paginado por offset (page_size max 100). */
    public function getShopPublishableStatus(int $globalItemId, int $offset = 0, int $pageSize = 100): array
    {
        return $this->get('get_shop_publishable_status', [
            'global_item_id' => $globalItemId,
            'offset' => $offset,
            'page_size' => $pageSize,
        ]);
    }

    /**
     * Quais campos do global item sincronizam automaticamente pra cada loja
     * (nome/descricao, midia, variacoes, preco, DTS). Ate' 50 lojas.
     *
     * @param array<int,array<string,mixed>> $shopSyncList
     */
    public function setSyncField(array $shopSyncList): array
    {
        return $this->post('set_sync_field', ['shop_sync_list' => array_values($shopSyncList)]);
    }

    // -------------------------------------------------------- categoria/marca

    /** Arvore de categorias globais. language: "en" | "zh-hans". */
    public function getCategory(string $language = 'en'): array
    {
        return $this->get('get_category', ['language' => $language]);
    }

    /** Sugestao de categoria pelo nome (+ imagem de capa opcional, image_id do media_space). */
    public function categoryRecommend(string $globalItemName, ?string $coverImageId = null): array
    {
        $query = ['global_item_name' => $globalItemName];
        if ($coverImageId !== null) {
            $query['global_product_cover_image'] = $coverImageId;
        }

        return $this->get('category_recommend', $query);
    }

    /** Marcas da categoria. status 1=normal, 2=pendente. Paginacao por offset. */
    public function getBrandList(int $categoryId, int $offset = 0, int $pageSize = 100, int $status = 1): array
    {
        return $this->get('get_brand_list', [
            'category_id' => $categoryId,
            'offset' => $offset,
            'page_size' => $pageSize,
            'status' => $status,
        ]);
    }

    /** Atributos das categorias (ate' 20 ids). language ex.: "en", "zh-Hans", "pt-BR". @param int[] $categoryIds */
    public function getAttributeTree(array $categoryIds, string $language = 'en'): array
    {
        return $this->get('get_attribute_tree', [
            'category_id_list' => implode(',', $categoryIds),
            'language' => $language,
        ]);
    }

    /** Atributos recomendados a partir do nome (+ categoria/imagem opcionais). */
    public function getRecommendAttribute(string $globalItemName, ?int $categoryId = null, ?string $coverImageId = null): array
    {
        $query = ['global_item_name' => $globalItemName];
        if ($categoryId !== null) {
            $query['category_id'] = $categoryId;
        }
        if ($coverImageId !== null) {
            $query['cover_image_id'] = $coverImageId;
        }

        return $this->get('get_recommend_attribute', $query);
    }

    /** Busca valores de um atributo por texto. limit [1,100]; cursor numerico. */
    public function searchGlobalAttributeValueList(int $attributeId, string $valueName, int $limit = 100, int $cursor = 0): array
    {
        return $this->post('search_global_attribute_value_list', [
            'attribute_id' => $attributeId,
            'value_name' => $valueName,
            'limit' => $limit,
            'cursor' => $cursor,
        ]);
    }

    /** Variacoes padronizadas (standardise tier variation) da categoria folha. */
    public function getVariations(int $categoryId): array
    {
        return $this->get('get_variations', ['category_id' => $categoryId]);
    }

    // -------------------------------------------------------------- size chart

    public function supportSizeChart(int $categoryId): array
    {
        return $this->get('support_size_chart', ['category_id' => $categoryId]);
    }

    /** Templates de size chart da categoria, paginado por cursor (string). */
    public function getSizeChartList(int $categoryId, int $pageSize = 20, ?string $cursor = null): array
    {
        $query = ['category_id' => $categoryId, 'page_size' => $pageSize];
        if ($cursor !== null) {
            $query['cursor'] = $cursor;
        }

        return $this->get('get_size_chart_list', $query);
    }

    /** language: "en" | "zh-Hans". */
    public function getSizeChartDetail(int $sizeChartId, string $language = 'en'): array
    {
        return $this->get('get_size_chart_detail', [
            'size_chart_id' => $sizeChartId,
            'language' => $language,
        ]);
    }

    /** Troca a IMAGEM de size chart do global item (image_id do media_space). */
    public function updateSizeChart(int $globalItemId, string $sizeChart): array
    {
        return $this->post('update_size_chart', [
            'global_item_id' => $globalItemId,
            'size_chart' => $sizeChart,
        ]);
    }
}
