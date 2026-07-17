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
}
