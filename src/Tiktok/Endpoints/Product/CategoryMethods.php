<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Product;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

/**
 * Categorias e atributos de categoria do TikTok Shop (versao 202309).
 *
 * `getAttributes` PROBADO ao vivo 2026-07-05 (categoria 700649 → 13 atributos
 * com value-lists: "Vida útil", "Formulário do produto", etc). E' o
 * pre-requisito do formulario dinamico de publicacao (value-lists por categoria).
 *
 * getCategories/getBrands seguem a mesma convencao /product/202309/... (nao
 * probados individualmente).
 */
class CategoryMethods extends BaseMethods
{
    private const VERSION = '202309';

    /**
     * Atributos (com value-lists) de uma categoria folha.
     * GET /product/202309/categories/{category_id}/attributes
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAttributes(string $categoryId, string $locale = 'pt-BR'): array
    {
        $resp = $this->makeRequest(
            HttpMethod::GET,
            '/product/'.self::VERSION.'/categories/'.$categoryId.'/attributes',
            ['locale' => $locale],
        );

        return $resp['data']['attributes'] ?? [];
    }

    /**
     * Arvore de categorias do seller.
     * GET /product/202309/categories
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCategories(string $locale = 'pt-BR'): array
    {
        $resp = $this->makeRequest(
            HttpMethod::GET,
            '/product/'.self::VERSION.'/categories',
            ['locale' => $locale],
        );

        return $resp['data']['categories'] ?? [];
    }

    /**
     * Marcas (campo obrigatorio no create de produto em muitas categorias).
     * GET /product/202309/brands
     *
     * @return array<int, array<string, mixed>>
     */
    public function getBrands(string $categoryId, int $pageSize = 100): array
    {
        $resp = $this->makeRequest(
            HttpMethod::GET,
            '/product/'.self::VERSION.'/brands',
            ['category_id' => $categoryId, 'page_size' => $pageSize],
        );

        return $resp['data']['brands'] ?? [];
    }
}
