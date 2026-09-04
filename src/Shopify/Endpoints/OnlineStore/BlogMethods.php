<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\OnlineStore;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Blog da loja online: blogs, artigos e comentarios.
 *
 * Recursos REST: `blog`, `article`, `comment`.
 */
class BlogMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    // ---------------------------------------------------------------- blogs

    /**
     * Lista os blogs.
     *
     * @param  array<string, mixed>  $params  since_id, handle, fields, limit
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/blogs', $params);
    }

    /**
     * Total de blogs.
     */
    public function count(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/blogs/count');
    }

    /**
     * Recupera um blog.
     */
    public function get(int|string $blogId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/blogs/{$blogId}", $params);
    }

    /**
     * Cria um blog. Embrulha em `blog`.
     *
     * @param  array<string, mixed>  $blog
     */
    public function create(array $blog): array
    {
        return $this->makeRequest(HttpMethod::POST, '/blogs', [], ['blog' => $blog]);
    }

    /**
     * Atualiza um blog. Embrulha em `blog`.
     *
     * @param  array<string, mixed>  $blog
     */
    public function update(int|string $blogId, array $blog): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/blogs/{$blogId}", [], ['blog' => $blog]);
    }

    /**
     * Remove um blog.
     */
    public function delete(int|string $blogId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/blogs/{$blogId}");
    }

    // ------------------------------------------------------------- articles

    /**
     * Lista os artigos de um blog.
     *
     * @param  array<string, mixed>  $params  since_id, created_at_min/max, published_status, handle, tag, author, limit, fields
     */
    public function listArticles(int|string $blogId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/blogs/{$blogId}/articles", $params);
    }

    /**
     * Itera todos os artigos de um blog (paginacao por cursor).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function eachArticle(int|string $blogId, array $params = [], int $limit = 250): \Generator
    {
        return $this->eachPage("/blogs/{$blogId}/articles.json", 'articles', $params, $limit);
    }

    /**
     * Total de artigos de um blog.
     *
     * @param  array<string, mixed>  $params  created_at_min/max, published_status
     */
    public function countArticles(int|string $blogId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/blogs/{$blogId}/articles/count", $params);
    }

    /**
     * Recupera um artigo.
     */
    public function getArticle(int|string $blogId, int|string $articleId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/blogs/{$blogId}/articles/{$articleId}", $params);
    }

    /**
     * Cria um artigo. Embrulha em `article`.
     *
     * @param  array<string, mixed>  $article
     */
    public function createArticle(int|string $blogId, array $article): array
    {
        return $this->makeRequest(HttpMethod::POST, "/blogs/{$blogId}/articles", [], ['article' => $article]);
    }

    /**
     * Atualiza um artigo. Embrulha em `article`.
     *
     * @param  array<string, mixed>  $article
     */
    public function updateArticle(int|string $blogId, int|string $articleId, array $article): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/blogs/{$blogId}/articles/{$articleId}", [], ['article' => $article]);
    }

    /**
     * Remove um artigo.
     */
    public function deleteArticle(int|string $blogId, int|string $articleId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/blogs/{$blogId}/articles/{$articleId}");
    }

    /**
     * Lista os autores de artigos da loja (todos os blogs).
     */
    public function articleAuthors(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/articles/authors');
    }

    /**
     * Lista as tags de artigos — da loja inteira ou de um blog especifico.
     *
     * @param  array<string, mixed>  $params  limit, popular
     */
    public function articleTags(int|string|null $blogId = null, array $params = []): array
    {
        $path = $blogId === null ? '/articles/tags' : "/blogs/{$blogId}/articles/tags";

        return $this->makeRequest(HttpMethod::GET, $path, $params);
    }

    // ------------------------------------------------------------- comments

    /**
     * Lista comentarios (filtrar por article_id/blog_id nos params).
     *
     * @param  array<string, mixed>  $params  article_id, blog_id, status, published_status, since_id, limit, fields
     */
    public function listComments(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/comments', $params);
    }

    /**
     * Total de comentarios.
     *
     * @param  array<string, mixed>  $params
     */
    public function countComments(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/comments/count', $params);
    }

    /**
     * Recupera um comentario.
     */
    public function getComment(int|string $commentId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/comments/{$commentId}", $params);
    }

    /**
     * Cria um comentario. Embrulha em `comment`.
     *
     * @param  array<string, mixed>  $comment
     */
    public function createComment(array $comment): array
    {
        return $this->makeRequest(HttpMethod::POST, '/comments', [], ['comment' => $comment]);
    }

    /**
     * Atualiza um comentario. Embrulha em `comment`.
     *
     * @param  array<string, mixed>  $comment
     */
    public function updateComment(int|string $commentId, array $comment): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/comments/{$commentId}", [], ['comment' => $comment]);
    }

    /**
     * Aprova um comentario pendente.
     */
    public function approveComment(int|string $commentId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/comments/{$commentId}/approve");
    }

    /**
     * Marca um comentario como spam.
     */
    public function spamComment(int|string $commentId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/comments/{$commentId}/spam");
    }

    /**
     * Desmarca um comentario como spam.
     */
    public function notSpamComment(int|string $commentId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/comments/{$commentId}/not_spam");
    }

    /**
     * Remove (oculta) um comentario.
     */
    public function removeComment(int|string $commentId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/comments/{$commentId}/remove");
    }

    /**
     * Restaura um comentario removido.
     */
    public function restoreComment(int|string $commentId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/comments/{$commentId}/restore");
    }
}
