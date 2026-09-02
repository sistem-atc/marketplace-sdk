<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Category;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Category\CategoryAttribute;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Category\CategoryPrediction;

class CategoryMethods extends BaseMethods
{
    /**
     * Lista categorias de um site (ex: MLB).
     */
    public function listSitesCategories(string $siteId = 'MLB'): array
    {
        return $this->makeRequest(HttpMethod::GET, "/sites/{$siteId}/categories");
    }

    /**
     * Detalhes de uma categoria (atributos, subcategorias).
     */
    public function get(string $categoryId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/categories/{$categoryId}");
    }

    /**
     * Busca os atributos de uma categoria (com tags required/allowed values).
     *
     * Endpoint dedicado /categories/{id}/attributes — o GET /categories/{id}
     * (metodo get()) NAO traz os atributos (so' settings/children), por isso
     * antes retornava vazio. Retorna a lista de atributos direto (array top-level).
     */
    /** @return list<CategoryAttribute> */
    public function attributes(string $categoryId): array
    {
        return array_map(
            fn (array $a) => CategoryAttribute::fromArray($a),
            (array) $this->makeRequest(HttpMethod::GET, "/categories/{$categoryId}/attributes"),
        );
    }

    /**
     * Preditor de categoria: sugere a melhor categoria baseada no titulo.
     * Devolve LISTA ordenada por confianca — o [0] e' o melhor palpite.
     *
     * @return list<CategoryPrediction>
     */
    public function predict(string $title, string $siteId = 'MLB'): array
    {
        return array_map(
            fn (array $p) => CategoryPrediction::fromArray($p),
            (array) $this->makeRequest(HttpMethod::GET, "/sites/{$siteId}/domain_discovery/search", [
                'q' => $title,
            ]),
        );
    }

    /**
     * Busca informacoes de dominio.
     */
    public function domain(string $domainId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/domains/{$domainId}");
    }


    // ── Atributos / ficha técnica (doc "Atributos" + "Publicar produtos") ─

    /**
     * Quais atributos `conditional_required` viram obrigatórios pra ESTE
     * anúncio (POST /categories/{id}/attributes/conditional). O body é o JSON
     * do item que você vai publicar (title, category_id, price, attributes...);
     * a resposta lista os atributos exigidos naquela combinação.
     *
     * @param  array<string, mixed>  $item
     * @return array<int, array<string, mixed>>
     */
    public function conditionalAttributes(string $categoryId, array $item): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/categories/'.rawurlencode($categoryId).'/attributes/conditional',
            [],
            $item,
        );
    }

    /**
     * Ficha técnica de ENTRADA da categoria (GET /categories/{id}/technical_specs/input):
     * `{groups[{id, label, components[{component, attributes[]}]}]}` — como
     * o formulário de publicação pede os atributos (obrigatório, tipo de
     * campo, valores). Versão por categoria do CatalogMethods::technicalSpecsInput().
     *
     * @return array<string, mixed>
     */
    public function technicalSpecsInput(string $categoryId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/categories/'.rawurlencode($categoryId).'/technical_specs/input');
    }

    /**
     * Ficha técnica de SAÍDA (GET /categories/{id}/technical_specs/output):
     * como os atributos são exibidos na VIP (main_title, groups, ordem).
     *
     * @return array<string, mixed>
     */
    public function technicalSpecsOutput(string $categoryId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/categories/'.rawurlencode($categoryId).'/technical_specs/output');
    }

    /**
     * Termos de venda da categoria (GET /categories/{id}/sale_terms):
     * WARRANTY_TYPE, WARRANTY_TIME, MANUFACTURING_TIME, INVOICE,
     * PURCHASE_MAX_QUANTITY... com tags (hidden, read_only, multivalued),
     * value_type e valores permitidos. Vão em `sale_terms[]` do item.
     *
     * @return array<int, array<string, mixed>>
     */
    public function saleTerms(string $categoryId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/categories/'.rawurlencode($categoryId).'/sale_terms');
    }

    /**
     * Valores mais usados de um atributo no domínio
     * (POST /catalog_domains/{domainId}/attributes/{attributeId}/top_values):
     * `[{id, name, metric}]`. Body opcional `{known_attributes[{id, value_id}]}`
     * restringe pelo que já se sabe (ex.: MODEL dado BRAND=Samsung).
     *
     * @param  array<int, array<string, mixed>>  $knownAttributes
     * @return array<int, array<string, mixed>>
     */
    public function attributeTopValues(string $domainId, string $attributeId, array $knownAttributes = []): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/catalog_domains/'.rawurlencode($domainId).'/attributes/'.rawurlencode($attributeId).'/top_values',
            [],
            $knownAttributes === [] ? [] : ['known_attributes' => array_values($knownAttributes)],
        );
    }
}
