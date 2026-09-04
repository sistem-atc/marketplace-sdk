<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\OnlineStore;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Pages (`pages`) — paginas estaticas da loja online.
 */
class PageMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    /**
     * Lista paginas (1 pagina). Filtros: limit, since_id, title, handle,
     * created_at_min/max, updated_at_min/max, published_at_min/max,
     * fields, published_status.
     *
     * @param  array<string, mixed>  $params
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/pages', $params);
    }

    /**
     * Itera TODAS as paginas seguindo o cursor (page_info).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function each(array $params = [], int $limit = 250): \Generator
    {
        yield from $this->eachPage('/pages', 'pages', $params, $limit);
    }

    /**
     * Conta paginas (mesmos filtros de `list()`).
     *
     * @param  array<string, mixed>  $params
     */
    public function count(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/pages/count', $params);
    }

    /**
     * Recupera uma pagina.
     */
    public function get(int|string $pageId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/pages/{$pageId}", $params);
    }

    /**
     * Cria uma pagina. Embrulha em `page`.
     *
     * @param  array<string, mixed>  $page
     */
    public function create(array $page): array
    {
        return $this->makeRequest(HttpMethod::POST, '/pages', [], ['page' => $page]);
    }

    /**
     * Atualiza uma pagina. Embrulha em `page`.
     *
     * @param  array<string, mixed>  $page
     */
    public function update(int|string $pageId, array $page): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/pages/{$pageId}", [], ['page' => $page]);
    }

    /**
     * Exclui uma pagina.
     */
    public function delete(int|string $pageId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/pages/{$pageId}");
    }
}
