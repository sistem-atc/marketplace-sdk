<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\OnlineStore;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Loja virtual (Admin API REST — `theme`, `redirect`, `script_tag`).
 */
class ThemeMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    // ---------------------------------------------------------------
    // Themes
    // ---------------------------------------------------------------

    /**
     * Lista temas da loja.
     *
     * @param  array<string, mixed>  $params  ex.: fields
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/themes', $params);
    }

    /**
     * Recupera um tema.
     *
     * @param  array<string, mixed>  $params  ex.: fields
     */
    public function get(int|string $themeId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/themes/{$themeId}", $params);
    }

    /**
     * Cria um tema (name + src zip; role opcional: main|unpublished|demo).
     *
     * @param  array<string, mixed>  $theme
     */
    public function create(array $theme): array
    {
        return $this->makeRequest(HttpMethod::POST, '/themes', [], ['theme' => $theme]);
    }

    /**
     * Atualiza um tema (ex.: ['role' => 'main'] publica o tema).
     *
     * @param  array<string, mixed>  $theme
     */
    public function update(int|string $themeId, array $theme): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/themes/{$themeId}", [], ['theme' => $theme]);
    }

    /**
     * Remove um tema.
     */
    public function delete(int|string $themeId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/themes/{$themeId}");
    }

    // ---------------------------------------------------------------
    // Redirects (URL redirects 301)
    // ---------------------------------------------------------------

    /**
     * Lista redirecionamentos (1 pagina, max 250).
     *
     * @param  array<string, mixed>  $params  ex.: limit, since_id, path, target, fields
     */
    public function listRedirects(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/redirects', $params);
    }

    /**
     * Itera TODOS os redirecionamentos (paginacao por cursor).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function eachRedirect(array $params = [], int $limit = 250): \Generator
    {
        return $this->eachPage('/redirects', 'redirects', $params, $limit);
    }

    /**
     * Conta redirecionamentos.
     *
     * @param  array<string, mixed>  $params  ex.: path, target
     */
    public function countRedirects(array $params = []): int
    {
        $response = $this->makeRequest(HttpMethod::GET, '/redirects/count', $params);

        return $response['count'] ?? 0;
    }

    /**
     * Recupera um redirecionamento.
     *
     * @param  array<string, mixed>  $params  ex.: fields
     */
    public function getRedirect(int|string $redirectId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/redirects/{$redirectId}", $params);
    }

    /**
     * Cria um redirecionamento. Ex.: ['path' => '/antigo', 'target' => '/novo'].
     *
     * @param  array<string, mixed>  $redirect
     */
    public function createRedirect(array $redirect): array
    {
        return $this->makeRequest(HttpMethod::POST, '/redirects', [], ['redirect' => $redirect]);
    }

    /**
     * Atualiza um redirecionamento.
     *
     * @param  array<string, mixed>  $redirect
     */
    public function updateRedirect(int|string $redirectId, array $redirect): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/redirects/{$redirectId}", [], ['redirect' => $redirect]);
    }

    /**
     * Remove um redirecionamento.
     */
    public function deleteRedirect(int|string $redirectId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/redirects/{$redirectId}");
    }

    // ---------------------------------------------------------------
    // Script tags (JS injetado na loja/checkout pelo app)
    // ---------------------------------------------------------------

    /**
     * Lista script tags do app (1 pagina, max 250).
     *
     * @param  array<string, mixed>  $params  ex.: limit, since_id, created_at_min/max, updated_at_min/max, src, fields
     */
    public function listScriptTags(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/script_tags', $params);
    }

    /**
     * Itera TODAS as script tags (paginacao por cursor).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function eachScriptTag(array $params = [], int $limit = 250): \Generator
    {
        return $this->eachPage('/script_tags', 'script_tags', $params, $limit);
    }

    /**
     * Conta script tags do app.
     *
     * @param  array<string, mixed>  $params  ex.: src
     */
    public function countScriptTags(array $params = []): int
    {
        $response = $this->makeRequest(HttpMethod::GET, '/script_tags/count', $params);

        return $response['count'] ?? 0;
    }

    /**
     * Recupera uma script tag.
     *
     * @param  array<string, mixed>  $params  ex.: fields
     */
    public function getScriptTag(int|string $scriptTagId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/script_tags/{$scriptTagId}", $params);
    }

    /**
     * Cria uma script tag. Ex.: ['event' => 'onload', 'src' => 'https://.../app.js'].
     *
     * @param  array<string, mixed>  $scriptTag
     */
    public function createScriptTag(array $scriptTag): array
    {
        return $this->makeRequest(HttpMethod::POST, '/script_tags', [], ['script_tag' => $scriptTag]);
    }

    /**
     * Atualiza uma script tag.
     *
     * @param  array<string, mixed>  $scriptTag
     */
    public function updateScriptTag(int|string $scriptTagId, array $scriptTag): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/script_tags/{$scriptTagId}", [], ['script_tag' => $scriptTag]);
    }

    /**
     * Remove uma script tag.
     */
    public function deleteScriptTag(int|string $scriptTagId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/script_tags/{$scriptTagId}");
    }
}
