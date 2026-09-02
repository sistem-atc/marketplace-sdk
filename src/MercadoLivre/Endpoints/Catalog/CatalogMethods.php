<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Catalog;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;

/**
 * Catálogo do Mercado Livre: buscador de produtos, Brand Central
 * (catalog_suggestions), fichas técnicas de domínio, sincronização de buybox
 * e tabelas de medidas (catalog/charts).
 */
class CatalogMethods extends BaseMethods
{
    // -----------------------------------------------------------------
    // Buscador de produtos de catálogo
    // -----------------------------------------------------------------

    /**
     * Busca produtos de catálogo (GET /products/search). Obrigatório
     * `site_id` + (`q` OU `product_identifier` = GTIN/EAN). Opcionais:
     * status (active|inactive), domain_id, listing_strategy
     * (catalog_required|open), skip_cache, offset, limit. Devolve
     * {keywords, paging, results[{id, status, domain_id, settings, name,
     * attributes, pictures, parent_id, children_ids}]}.
     *
     * @param  array<string,mixed>  $filters
     */
    public function searchProducts(array $filters, string $siteId = 'MLB'): array
    {
        return $this->makeRequest(HttpMethod::GET, '/products/search', array_merge(['site_id' => $siteId], $filters));
    }

    /**
     * Busca produtos de catálogo por atributos (POST /products/search).
     * Body {domain_id, site_id, status, attributes:[{id, value_id|value_name}]}.
     *
     * @param  array<int, array<string,mixed>>  $attributes
     */
    public function searchProductsByAttributes(string $domainId, array $attributes, string $siteId = 'MLB', ?string $status = 'active'): array
    {
        $body = ['domain_id' => $domainId, 'site_id' => $siteId, 'attributes' => array_values($attributes)];
        if ($status !== null) {
            $body['status'] = $status;
        }

        return $this->makeRequest(HttpMethod::POST, '/products/search', [], $body);
    }

    /**
     * Produto de catálogo (GET /products/{id}): name, domain_id, status,
     * attributes, pictures, buy_box_winner, parent_id/children_ids (famílias).
     */
    public function product(string $productId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/products/'.rawurlencode($productId));
    }

    /**
     * Categorias do site associadas a um domínio de catálogo
     * (GET /catalog_domains/{domain}/categories) → [{id, name}].
     */
    public function domainCategories(string $domainId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/catalog_domains/'.rawurlencode($domainId).'/categories');
    }

    // -----------------------------------------------------------------
    // Fichas técnicas de domínio
    // -----------------------------------------------------------------

    /**
     * Ficha técnica de um domínio (GET /domains/{id}/technical_specs) —
     * input (grupos/componentes para montar formulário) + output. Para o
     * fluxo Brand Central passe `channel_id=catalog_suggestions` na query.
     *
     * @param  array<string,mixed>  $query
     */
    public function technicalSpecs(string $domainId, array $query = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/domains/'.rawurlencode($domainId).'/technical_specs', $query);
    }

    /**
     * Só a parte `input` da ficha técnica (GET /domains/{id}/technical_specs/input).
     *
     * @param  array<string,mixed>  $query
     */
    public function technicalSpecsInput(string $domainId, array $query = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/domains/'.rawurlencode($domainId).'/technical_specs/input', $query);
    }

    /**
     * Só a parte `output` da ficha técnica (GET /domains/{id}/technical_specs/output).
     *
     * @param  array<string,mixed>  $query
     */
    public function technicalSpecsOutput(string $domainId, array $query = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/domains/'.rawurlencode($domainId).'/technical_specs/output', $query);
    }

    /**
     * Ficha técnica condicionada aos atributos já escolhidos
     * (POST /domains/{id}/technical_specs?section=grids). Usado para tabelas
     * de medidas: enviar BRAND/GENDER devolve os atributos de tamanho válidos.
     *
     * @param  array<int, array<string,mixed>>  $attributes
     */
    public function technicalSpecsFor(string $domainId, array $attributes, ?string $section = 'grids'): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/domains/'.rawurlencode($domainId).'/technical_specs',
            $section !== null ? ['section' => $section] : [],
            ['attributes' => array_values($attributes)]
        );
    }

    // -----------------------------------------------------------------
    // Brand Central — sugestões de produto de catálogo
    // -----------------------------------------------------------------

    /**
     * Cota de sugestões do seller (GET /catalog_suggestions/users/{id}/quota).
     */
    public function suggestionsQuota(int|string $userId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/catalog_suggestions/users/{$userId}/quota");
    }

    /**
     * Domínios do site abertos a sugestão de produto
     * (GET /catalog_suggestions/domains/{site}/available/full).
     */
    public function suggestionAvailableDomains(string $siteId = 'MLB'): array
    {
        return $this->makeRequest(HttpMethod::GET, '/catalog_suggestions/domains/'.rawurlencode($siteId).'/available/full');
    }

    /**
     * Cria uma sugestão de produto (POST /catalog_suggestions). Body
     * {title, domain_id, pictures:[{id}], attributes:[{id, values:[{id?, name}]}]}.
     * Fotos precisam ter sido enviadas antes (pictures/upload).
     *
     * @param  array<string,mixed>  $body
     */
    public function createSuggestion(array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/catalog_suggestions', [], $body);
    }

    /**
     * Detalhe de uma sugestão (GET /catalog_suggestions/{id}): status
     * (UNDER_REVIEW, ...), sub_status, attributes, pictures.
     */
    public function suggestion(int|string $suggestionId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/catalog_suggestions/'.rawurlencode((string) $suggestionId));
    }

    /**
     * Altera título/atributos/fotos de uma sugestão (PUT /catalog_suggestions/{id}).
     *
     * @param  array<string,mixed>  $body
     */
    public function updateSuggestion(int|string $suggestionId, array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/catalog_suggestions/'.rawurlencode((string) $suggestionId), [], $body);
    }

    /**
     * Adiciona a descrição da sugestão (POST /catalog_suggestions/{id}/description
     * body {plain_text}). Resposta 200 sem corpo → array vazio.
     */
    public function setSuggestionDescription(int|string $suggestionId, string $plainText): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/catalog_suggestions/'.rawurlencode((string) $suggestionId).'/description',
            [],
            ['plain_text' => $plainText]
        );
    }

    /**
     * Substitui a descrição já enviada (PUT /catalog_suggestions/{id}/description).
     */
    public function updateSuggestionDescription(int|string $suggestionId, string $plainText): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            '/catalog_suggestions/'.rawurlencode((string) $suggestionId).'/description',
            [],
            ['plain_text' => $plainText]
        );
    }

    /**
     * Resultado das validações internas da sugestão
     * (GET /catalog_suggestions/{id}/validations).
     */
    public function suggestionValidations(int|string $suggestionId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/catalog_suggestions/'.rawurlencode((string) $suggestionId).'/validations');
    }

    /**
     * Lista as sugestões do seller (GET /catalog_suggestions/users/{id}/suggestions/search)
     * → {total, suggestions[{id, status, sub_status, title, domain_id, ...}]}.
     *
     * @param  array<string,mixed>  $query
     */
    public function searchUserSuggestions(int|string $userId, array $query = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/catalog_suggestions/users/{$userId}/suggestions/search", $query);
    }

    // -----------------------------------------------------------------
    // Buybox sync (publicação de catálogo x anúncio de marketplace)
    // -----------------------------------------------------------------

    /**
     * Estado de sincronização do anúncio de catálogo com o de marketplace
     * (GET /public/buybox/sync/{item}) → {item_id, status: SYNC|..., relations}.
     * Exige header `x-public: True`.
     */
    public function buyboxSyncStatus(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/public/buybox/sync/'.rawurlencode($itemId), [], [], 0, ['x-public' => 'True']);
    }

    /**
     * Força a sincronização (POST /public/buybox/sync body {id}). 200 ok,
     * 422/500 erro. Header `x-public: True`.
     */
    public function buyboxSync(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::POST, '/public/buybox/sync', [], ['id' => $itemId], 0, ['x-public' => 'True']);
    }

    // -----------------------------------------------------------------
    // Tabelas de medidas (catalog/charts)
    // -----------------------------------------------------------------

    /**
     * Domínios do site com tabela de medidas habilitada
     * (GET /catalog/charts/{site}/configurations/active_domains) → {domains[{domain_id}]}.
     */
    public function activeChartDomains(string $siteId = 'MLB'): array
    {
        return $this->makeRequest(HttpMethod::GET, '/catalog/charts/'.rawurlencode($siteId).'/configurations/active_domains');
    }

    /**
     * Domínios configurados por tipo de tabela (POST /catalog/charts/domains/search
     * body {site_id, type: BRAND|STANDARD}).
     */
    public function searchChartDomains(string $type, string $siteId = 'MLB'): array
    {
        return $this->makeRequest(HttpMethod::POST, '/catalog/charts/domains/search', [], ['site_id' => $siteId, 'type' => $type]);
    }

    /**
     * Busca tabelas de medidas disponíveis (POST /catalog/charts/search?offset&limit).
     * Body {domain_id (SEM prefixo do site, ex.: SNEAKERS), site_id, seller_id,
     * attributes:[{id: GENDER|BRAND, values:[{name}]}], main_attribute_id?}.
     * O ML espera o header `x-caller-id` com o seller_id.
     *
     * @param  array<string,mixed>  $body
     */
    public function searchCharts(array $body, int $offset = 0, int $limit = 100, int|string|null $callerId = null): array
    {
        $callerId ??= $body['seller_id'] ?? null;

        return $this->makeRequest(
            HttpMethod::POST,
            '/catalog/charts/search',
            ['offset' => $offset, 'limit' => $limit],
            $body,
            0,
            $callerId !== null ? ['x-caller-id' => (string) $callerId] : []
        );
    }

    /**
     * Cria uma tabela de medidas (POST /catalog/charts). Body {names{MLB:...},
     * domain_id, site_id, main_attribute?, attributes, measure_type?, rows[...]}.
     *
     * @param  array<string,mixed>  $body
     */
    public function createChart(array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/catalog/charts', [], $body);
    }

    /**
     * Detalhe de uma tabela de medidas (GET /catalog/charts/{id}), com rows.
     */
    public function chart(int|string $chartId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/catalog/charts/'.rawurlencode((string) $chartId));
    }

    /**
     * Atualiza dados da tabela (PUT /catalog/charts/{id}), ex.: {names{...}}.
     *
     * @param  array<string,mixed>  $body
     */
    public function updateChart(int|string $chartId, array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/catalog/charts/'.rawurlencode((string) $chartId), [], $body);
    }

    /**
     * Exclui a tabela (DELETE /catalog/charts/{id}). Falha se houver anúncio
     * ativo associado à tabela.
     */
    public function deleteChart(int|string $chartId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, '/catalog/charts/'.rawurlencode((string) $chartId));
    }

    /**
     * Adiciona uma linha (tamanho) na tabela (POST /catalog/charts/{id}/rows).
     * Body {attributes:[{id, values:[{name}]}], sites?}.
     *
     * @param  array<string,mixed>  $body
     */
    public function addChartRow(int|string $chartId, array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/catalog/charts/'.rawurlencode((string) $chartId).'/rows', [], $body);
    }

    /**
     * Altera uma linha da tabela (PUT /catalog/charts/{chart}/rows/{row}).
     *
     * @param  array<string,mixed>  $body
     */
    public function updateChartRow(int|string $chartId, int|string $rowId, array $body): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            '/catalog/charts/'.rawurlencode((string) $chartId).'/rows/'.rawurlencode((string) $rowId),
            [],
            $body
        );
    }
}
