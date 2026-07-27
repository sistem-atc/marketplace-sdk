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
}
