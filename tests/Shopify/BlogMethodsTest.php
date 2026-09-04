<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\OnlineStore\BlogMethods;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopifyRest1BlogIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shpat_x',
        refreshToken: null,
        settings: ['shop_domain' => 'loja-teste.myshopify.com'],
        active: true,
        expired: false,
    );
}

function shopifyRest1Blog(): BlogMethods
{
    $integration = shopifyRest1BlogIntegration();

    return new BlogMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL completa + header do token (+ body, quando informado).
 */
function shopifyRest1BlogAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(function (Request $request) use ($method, $url, $body): bool {
        return $request->method() === $method
            && $request->url() === $url
            && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
            && ($body === null || $request->data() === $body);
    });
}

beforeEach(function () {
    config(['marketplaces.shopify.api_version' => '2024-04']);
    Http::preventStrayRequests();
});

describe('Shopify BlogMethods', function () {
    it('list: GET /blogs.json?limit=10', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->list(['limit' => 10]))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/blogs.json?limit=10');
    });

    it('count: GET /blogs/count.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->count())->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/blogs/count.json');
    });

    it('get: GET /blogs/1.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->get(1))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/blogs/1.json');
    });

    it('create: POST /blogs.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->create(['title' => 'Novidades']))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/blogs.json', ['blog' => ['title' => 'Novidades']]);
    });

    it('update: PUT /blogs/1.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->update(1, ['title' => 'News']))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('PUT', 'https://loja-teste.myshopify.com/admin/api/2024-04/blogs/1.json', ['blog' => ['title' => 'News']]);
    });

    it('delete: DELETE /blogs/1.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->delete(1))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('DELETE', 'https://loja-teste.myshopify.com/admin/api/2024-04/blogs/1.json');
    });

    it('listArticles: GET /blogs/1/articles.json?tag=x', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->listArticles(1, ['tag' => 'x']))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/blogs/1/articles.json?tag=x');
    });

    it('countArticles: GET /blogs/1/articles/count.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->countArticles(1))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/blogs/1/articles/count.json');
    });

    it('getArticle: GET /blogs/1/articles/2.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->getArticle(1, 2))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/blogs/1/articles/2.json');
    });

    it('createArticle: POST /blogs/1/articles.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->createArticle(1, ['title' => 'Oi']))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/blogs/1/articles.json', ['article' => ['title' => 'Oi']]);
    });

    it('updateArticle: PUT /blogs/1/articles/2.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->updateArticle(1, 2, ['title' => 'Tchau']))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('PUT', 'https://loja-teste.myshopify.com/admin/api/2024-04/blogs/1/articles/2.json', ['article' => ['title' => 'Tchau']]);
    });

    it('deleteArticle: DELETE /blogs/1/articles/2.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->deleteArticle(1, 2))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('DELETE', 'https://loja-teste.myshopify.com/admin/api/2024-04/blogs/1/articles/2.json');
    });

    it('articleAuthors: GET /articles/authors.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->articleAuthors())->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/articles/authors.json');
    });

    it('articleTags (loja): GET /articles/tags.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->articleTags())->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/articles/tags.json');
    });

    it('articleTags (blog): GET /blogs/1/articles/tags.json?popular=1', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->articleTags(1, ['popular' => 1]))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/blogs/1/articles/tags.json?popular=1');
    });

    it('listComments: GET /comments.json?article_id=2', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->listComments(['article_id' => 2]))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/comments.json?article_id=2');
    });

    it('countComments: GET /comments/count.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->countComments())->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/comments/count.json');
    });

    it('getComment: GET /comments/5.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->getComment(5))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/comments/5.json');
    });

    it('createComment: POST /comments.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->createComment(['body' => 'ok']))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/comments.json', ['comment' => ['body' => 'ok']]);
    });

    it('updateComment: PUT /comments/5.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->updateComment(5, ['body' => 'edit']))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('PUT', 'https://loja-teste.myshopify.com/admin/api/2024-04/comments/5.json', ['comment' => ['body' => 'edit']]);
    });

    it('approveComment: POST /comments/5/approve.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->approveComment(5))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/comments/5/approve.json');
    });

    it('spamComment: POST /comments/5/spam.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->spamComment(5))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/comments/5/spam.json');
    });

    it('notSpamComment: POST /comments/5/not_spam.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->notSpamComment(5))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/comments/5/not_spam.json');
    });

    it('removeComment: POST /comments/5/remove.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->removeComment(5))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/comments/5/remove.json');
    });

    it('restoreComment: POST /comments/5/restore.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Blog()->restoreComment(5))->toBe(['ok' => true]);

        shopifyRest1BlogAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/comments/5/restore.json');
    });

    it('eachArticle: segue o Link header (page_info) ate esgotar', function () {
        Http::fake([
            '*/blogs/1/articles.json*' => Http::sequence()
                ->push(['articles' => [['id' => 1], ['id' => 2]]], 200, [
                    'Link' => '<https://loja-teste.myshopify.com/admin/api/2024-04/blogs/1/articles.json?limit=250&page_info=CURSOR2>; rel="next"',
                ])
                ->push(['articles' => [['id' => 3]]], 200, []),
        ]);

        $items = iterator_to_array(shopifyRest1Blog()->eachArticle(1));

        expect(array_column($items, 'id'))->toBe([1, 2, 3]);

        Http::assertSent(fn (Request $r) => $r->url() === 'https://loja-teste.myshopify.com/admin/api/2024-04/blogs/1/articles.json?limit=250&page_info=CURSOR2');
    });
});
