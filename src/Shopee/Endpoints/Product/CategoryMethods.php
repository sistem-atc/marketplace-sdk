<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Product;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Catalogo de CATEGORIAS e ATRIBUTOS da Shopee (Product API v2).
 *
 * E' o equivalente ao /categories/{id}/attributes do Mercado Livre: define
 * quais campos aquela categoria exige pra que um item seja aceito.
 *
 * Rotas do mesmo modulo `product` que get_item_list / get_item_base_info ja'
 * usam — nao ha' escopo adicional pra LER categoria e atributo. O que costuma
 * exigir liberacao extra no Partner Center e' o `add_item` (CRIAR item), nao
 * esta leitura.
 *
 * A arvore vem ACHATADA (cada no' traz o proprio parent_category_id), entao
 * nao ha' descida recursiva como no ML — uma chamada devolve tudo.
 */
class CategoryMethods extends BaseMethods
{
    /**
     * Arvore de categorias (achatada).
     *
     * @param  string  $language  idioma dos rotulos (pt-br no Brasil)
     * @return array<int, array<string, mixed>> lista de category_list
     */
    public function getCategories(string $language = 'pt-br'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_category', [
            'language' => $language,
        ]);

        return $response['response']['category_list'] ?? [];
    }

    /**
     * Atributos exigidos/aceitos por UMA categoria.
     *
     * Cada item traz `attribute_id`, `original_attribute_name`,
     * `display_attribute_name`, `is_mandatory`, `input_type` e a lista
     * `attribute_value_list` quando o dominio e' fechado.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAttributes(int $categoryId, string $language = 'pt-br'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_attributes', [
            'category_id' => $categoryId,
            'language' => $language,
        ]);

        return $response['response']['attribute_list'] ?? [];
    }

    /**
     * Marcas aceitas na categoria. A Shopee valida marca contra a lista dela —
     * texto livre e' recusado no cadastro.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getBrands(int $categoryId, int $offset = 0, int $pageSize = 100): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_brand_list', [
            'category_id' => $categoryId,
            'offset' => $offset,
            'page_size' => $pageSize,
            // 1 = so' marcas aprovadas pra essa categoria.
            'status' => 1,
        ]);

        return $response['response']['brand_list'] ?? [];
    }

    /**
     * Arvore de atributos de ate 20 categorias de uma vez (versao nova do
     * get_attributes: traz atributos-filho condicionais e opcoes padronizadas).
     *
     * GET /api/v2/product/get_attribute_tree
     *
     * @param  array<int, int>  $categoryIds  max 20
     * @return array<int, array<string, mixed>> response.list[] (um por categoria)
     */
    public function getAttributeTree(array $categoryIds, string $language = 'pt-br'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_attribute_tree', [
            'category_id_list' => implode(',', $categoryIds),
            'language' => $language,
        ]);

        return $response['response']['list'] ?? [];
    }

    /**
     * Busca valores de um atributo por palavra-chave (atributos com dominio
     * grande, ex.: marca/modelo). Cursor numerico; limit 1..100.
     *
     * POST /api/v2/product/search_attribute_value_list
     *
     * @return array<string, mixed>
     */
    public function searchAttributeValueList(int $attributeId, ?string $valueName = null, int $cursor = 0, int $limit = 100): array
    {
        $body = ['attribute_id' => $attributeId, 'cursor' => $cursor, 'limit' => $limit];
        if ($valueName !== null && $valueName !== '') {
            $body['value_name'] = $valueName;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/search_attribute_value_list', [], $body);

        return $response['response'] ?? [];
    }

    /**
     * Categoria sugerida pela Shopee a partir do nome (e opcionalmente da
     * capa — image_id vindo do media_space/upload_image).
     *
     * GET /api/v2/product/category_recommend
     *
     * @return array<int, int> response.category_id[] (ranking)
     */
    public function recommendCategory(string $itemName, ?string $productCoverImageId = null): array
    {
        $query = ['item_name' => $itemName];
        if ($productCoverImageId !== null) {
            $query['product_cover_image'] = $productCoverImageId;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/category_recommend', $query);

        return $response['response']['category_id'] ?? [];
    }

    /**
     * Atributos/valores sugeridos pra um item dado nome + categoria (+ capa).
     *
     * GET /api/v2/product/get_recommend_attribute
     *
     * @return array<int, array<string, mixed>> response.attribute_list[]
     */
    public function getRecommendAttributes(string $itemName, int $categoryId, ?int $coverImageId = null): array
    {
        $query = ['item_name' => $itemName, 'category_id' => $categoryId];
        if ($coverImageId !== null) {
            $query['cover_image_id'] = $coverImageId;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_recommend_attribute', $query);

        return $response['response']['attribute_list'] ?? [];
    }

    /**
     * Pede o cadastro de uma marca nova (a Shopee so' aceita marca da lista).
     * Obrigatorios: original_brand_name, category_list[] (L1/L2),
     * product_image{image_id_list[]}, brand_region. Se o nome bater na
     * blacklist, brand_registration_website passa a ser exigido.
     *
     * POST /api/v2/product/register_brand
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed> response{brand_id, original_brand_name}
     */
    public function registerBrand(array $payload): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/register_brand', [], $payload);

        return $response['response'] ?? [];
    }

    /**
     * Regras de certificacao exigidas pra uma categoria/atributos (ex.: ANVISA,
     * INMETRO). Ambos os parametros sao opcionais no catalogo.
     *
     * POST /api/v2/product/get_product_certification_rule
     *
     * @param  array<int, array<string, mixed>>  $attributeList
     * @return array<int, array<string, mixed>> response.certification_rule_list[]
     */
    public function getProductCertificationRule(?int $categoryId = null, array $attributeList = []): array
    {
        $body = [];
        if ($categoryId !== null) {
            $body['category_id'] = $categoryId;
        }
        if ($attributeList !== []) {
            $body['attribute_list'] = array_values($attributeList);
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/product/get_product_certification_rule', [], $body);

        return $response['response']['certification_rule_list'] ?? [];
    }
}
