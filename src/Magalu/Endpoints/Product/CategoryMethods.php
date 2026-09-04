<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Product;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;

/**
 * CATEGORIAS e ATRIBUTOS do portfolio Magalu.
 *
 * E' o equivalente ao /categories/{id}/attributes do Mercado Livre: define
 * quais campos aquela categoria exige pra que um SKU seja aceito.
 *
 * DUAS LISTAS, NAO UMA — e essa e' a diferenca do Magalu pros outros MPs:
 *
 *   /attributes → atributos de VARIACAO (cor, voltagem, tamanho: o que
 *                 distingue um SKU irmao do outro)
 *   /datasheet  → FICHA TECNICA (o que descreve o produto: material,
 *                 potencia, composicao)
 *
 * Pra saber tudo que a categoria exige e' preciso consultar as duas.
 *
 * `required` NAO e' booleano: vem como `required` | `recommended` |
 * `optional`. Tratar como bool faria todo "recomendado" virar obrigatorio e
 * travar cadastro que a Magalu aceitaria.
 *
 * Escopos: open:portfolio-categories-seller:read e
 *          open:portfolio-categories-channel:read
 */
class CategoryMethods extends BaseMethods
{
    /**
     * Hierarquia completa de categorias.
     *
     * @return array<int, array<string, mixed>>
     */
    public function hierarchy(int $limit = 100, int $offset = 0): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/category/hierarchy', [
            '_limit' => $limit,
            '_offset' => $offset,
        ]);

        return $response['results'] ?? [];
    }

    /**
     * Busca categorias por nome ou id.
     *
     * @return array<int, array<string, mixed>>
     */
    public function search(?string $term = null, int $limit = 100, int $offset = 0): array
    {
        $query = ['_limit' => $limit, '_offset' => $offset];

        if ($term !== null && $term !== '') {
            $query['description'] = $term;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/category', $query);

        return $response['results'] ?? [];
    }

    /**
     * Atributos de VARIACAO da categoria (o que diferencia SKUs irmaos).
     *
     * O id da categoria e' UUID, nao numerico.
     *
     * @return array<int, array<string, mixed>>
     */
    public function attributes(string $categoryId): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/seller/v1/portfolios/category/{$categoryId}/attributes",
        );

        return $response['results'] ?? [];
    }

    /**
     * FICHA TECNICA da categoria (o que descreve o produto).
     *
     * @return array<int, array<string, mixed>>
     */
    public function datasheet(string $categoryId): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/seller/v1/portfolios/category/{$categoryId}/datasheet",
        );

        return $response['results'] ?? [];
    }


    // ------------------------------------------------------------------
    // Paths conforme a doc atual (plural `/portfolios/categories`). Os metodos
    // acima usam o singular `/portfolios/category`, que respondia em 2026-06;
    // se o singular passar a dar 404, migre pra estes. Todos devolvem o
    // envelope cru (`meta` + `results`), paginado por `_limit`/`_offset`/`_sort`.
    // ------------------------------------------------------------------

    /**
     * Busca categorias (GET /seller/v1/portfolios/categories). Filtros: `id`, `name`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function listCategories(array $filters = [], int $limit = 50, int $offset = 0, ?string $sort = null): array
    {
        $query = array_merge($filters, ['_limit' => $limit, '_offset' => $offset]);
        if ($sort !== null) $query['_sort'] = $sort;

        return $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/categories', $query);
    }

    /**
     * Hierarquia (GET /seller/v1/portfolios/categories/hierarchy).
     * `root_only=true` so' o 1o nivel; `category_id` / `parent_id` filtram a arvore.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function listHierarchy(array $filters = [], int $limit = 50, int $offset = 0, ?string $sort = null): array
    {
        $query = array_merge($filters, ['_limit' => $limit, '_offset' => $offset]);
        if ($sort !== null) $query['_sort'] = $sort;

        return $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/categories/hierarchy', $query);
    }

    /**
     * Atributos de variacao (GET /seller/v1/portfolios/categories/{id}/attributes).
     * `required`: required|optional|recommended.
     *
     * @return array<string, mixed>
     */
    public function listAttributes(string $categoryId, ?string $required = null, int $limit = 50, int $offset = 0): array
    {
        $query = ['_limit' => $limit, '_offset' => $offset];
        if ($required !== null) $query['required'] = $required;

        return $this->makeRequest(HttpMethod::GET, "/seller/v1/portfolios/categories/{$categoryId}/attributes", $query);
    }

    /**
     * Ficha tecnica (GET /seller/v1/portfolios/categories/{id}/datasheet).
     *
     * @return array<string, mixed>
     */
    public function listDatasheet(string $categoryId, ?string $required = null, int $limit = 50, int $offset = 0): array
    {
        $query = ['_limit' => $limit, '_offset' => $offset];
        if ($required !== null) $query['required'] = $required;

        return $this->makeRequest(HttpMethod::GET, "/seller/v1/portfolios/categories/{$categoryId}/datasheet", $query);
    }

    // ------------------------------------------------------------------
    // Visao CANAL (escopo open:portfolio-categories-channel:read) — catalogo
    // de atributos global do canal, nao do seller.
    // ------------------------------------------------------------------

    /**
     * Atributos do canal (GET /channel/v1/portfolios/attributes).
     * Filtros: `attribute_id`, `name` (exato), `active`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function channelAttributes(array $filters = [], int $limit = 50, int $offset = 0, ?string $sort = null): array
    {
        $query = array_merge($filters, ['_limit' => $limit, '_offset' => $offset]);
        if ($sort !== null) $query['_sort'] = $sort;

        return $this->makeRequest(HttpMethod::GET, '/channel/v1/portfolios/attributes', $query);
    }

    /**
     * Atributos de variacao da categoria na visao canal
     * (GET /channel/v1/portfolios/categories/{id}/attributes).
     *
     * @return array<string, mixed>
     */
    public function channelCategoryAttributes(string $categoryId, ?string $required = null): array
    {
        $query = $required !== null ? ['required' => $required] : [];

        return $this->makeRequest(HttpMethod::GET, "/channel/v1/portfolios/categories/{$categoryId}/attributes", $query);
    }

    /**
     * Ficha tecnica da categoria na visao canal
     * (GET /channel/v1/portfolios/categories/{id}/datasheet).
     *
     * @return array<string, mixed>
     */
    public function channelCategoryDatasheet(string $categoryId, ?string $required = null): array
    {
        $query = $required !== null ? ['required' => $required] : [];

        return $this->makeRequest(HttpMethod::GET, "/channel/v1/portfolios/categories/{$categoryId}/datasheet", $query);
    }
}
