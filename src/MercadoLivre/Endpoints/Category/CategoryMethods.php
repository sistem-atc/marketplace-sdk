<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Category;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

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
     * Busca atributos obrigatorios de uma categoria.
     */
    public function attributes(string $categoryId): array
    {
        $category = $this->get($categoryId);
        return $category['attributes'] ?? [];
    }

    /**
     * Preditor de categoria: sugere a melhor categoria baseada no titulo.
     */
    public function predict(string $title, string $siteId = 'MLB'): array
    {
        return $this->makeRequest(HttpMethod::GET, "/sites/{$siteId}/domain_discovery/search", [
            'q' => $title
        ]);
    }

    /**
     * Busca informacoes de dominio.
     */
    public function domain(string $domainId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/domains/{$domainId}");
    }
}
