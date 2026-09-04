<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Exceptions\ShopifyGraphQLException;
use SistemAtc\Marketplaces\Shopify\Exceptions\ShopifyRequestException;
use SistemAtc\Marketplaces\Shopify\GraphQL\Admin;
use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\OrderMutations;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\OrderQueries;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopifyGqlClientIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shpat_x',
        refreshToken: null,
        settings: ['shop_domain' => 'loja-teste.myshopify.com'],
        active: true,
        expired: false,
    );
}

/** @return array{0: GraphQLClient, 1: ArrayObject<int,int>} client + registro das esperas */
function shopifyGqlClientWithSleeps(): array
{
    $sleeps = new ArrayObject;
    $client = GraphQLClient::forIntegration(shopifyGqlClientIntegration())
        ->withSleeper(function (int $s) use ($sleeps) { $sleeps[] = $s; });

    return [$client, $sleeps];
}

beforeEach(function () {
    config(['marketplaces.shopify.graphql_api_version' => '2026-07']);
    Http::preventStrayRequests();
});

describe('Shopify GraphQLClient', function () {
    it('faz POST /graphql.json na versao GraphQL com token e body {query, variables}', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['shop' => ['name' => 'Loja']]], 200)]);

        $data = GraphQLClient::forIntegration(shopifyGqlClientIntegration())
            ->query('query shop { shop { name } }', ['first' => 1]);

        expect($data)->toBe(['shop' => ['name' => 'Loja']]);
        Http::assertSent(fn (Request $r) => $r->method() === 'POST'
            && $r->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
            && $r->hasHeader('X-Shopify-Access-Token', 'shpat_x')
            && $r->hasHeader('Content-Type', 'application/json')
            && json_decode($r->body(), true) === ['query' => 'query shop { shop { name } }', 'variables' => ['first' => 1]]);
    });

    it('usa a versao do config marketplaces.shopify.graphql_api_version', function () {
        config(['marketplaces.shopify.graphql_api_version' => '2025-10']);
        Http::fake(['*/graphql.json' => Http::response(['data' => []], 200)]);

        GraphQLClient::forIntegration(shopifyGqlClientIntegration())->query('{ shop { id } }');

        Http::assertSent(fn (Request $r) => str_contains($r->url(), '/admin/api/2025-10/graphql.json'));
    });

    it('lanca ShopifyGraphQLException quando a resposta traz errors', function () {
        Http::fake(['*/graphql.json' => Http::response([
            'errors' => [['message' => "Field 'foo' doesn't exist on type 'QueryRoot'", 'extensions' => ['code' => 'undefinedField']]],
        ], 200)]);

        try {
            GraphQLClient::forIntegration(shopifyGqlClientIntegration())->query('{ foo }');
            $this->fail('deveria lancar');
        } catch (ShopifyGraphQLException $e) {
            expect($e->getMessage())->toContain("Field 'foo' doesn't exist")
                ->and($e->errors())->toHaveCount(1)
                ->and($e->isThrottled())->toBeFalse();
        }
    });

    it('nao trata userErrors — devolve dentro do payload', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['orderUpdate' => [
            'order' => null, 'userErrors' => [['field' => ['input', 'id'], 'message' => 'Order not found']],
        ]]], 200)]);

        $payload = (new OrderMutations(GraphQLClient::forIntegration(shopifyGqlClientIntegration())))
            ->orderUpdate(['input' => ['id' => 'gid://shopify/Order/1']]);

        expect($payload['userErrors'][0]['message'])->toBe('Order not found');
    });

    it('lanca ShopifyRequestException em erro HTTP nao-retryavel', function () {
        Http::fake(['*/graphql.json' => Http::response(['errors' => 'Invalid API key'], 403)]);

        expect(fn () => GraphQLClient::forIntegration(shopifyGqlClientIntegration())->query('{ shop { id } }'))
            ->toThrow(ShopifyRequestException::class);
    });

    it('faz retry em THROTTLED esperando pelo throttleStatus e devolve na segunda tentativa', function () {
        Http::fake(['*/graphql.json' => Http::sequence()
            ->push([
                'errors' => [['message' => 'Throttled', 'extensions' => ['code' => 'THROTTLED']]],
                'extensions' => ['cost' => ['requestedQueryCost' => 500, 'throttleStatus' => ['currentlyAvailable' => 100, 'restoreRate' => 50]]],
            ], 200)
            ->push(['data' => ['orders' => ['edges' => []]]], 200)]);

        [$client, $sleeps] = shopifyGqlClientWithSleeps();
        $data = $client->query('{ orders(first: 1) { edges { node { id } } } }');

        expect($data)->toBe(['orders' => ['edges' => []]])
            ->and($sleeps->getArrayCopy())->toBe([8]); // (500-100)/50
        Http::assertSentCount(2);
    });

    it('faz retry em HTTP 429 respeitando Retry-After', function () {
        Http::fake(['*/graphql.json' => Http::sequence()
            ->push('', 429, ['Retry-After' => '3'])
            ->push(['data' => ['ok' => 1]], 200)]);

        [$client, $sleeps] = shopifyGqlClientWithSleeps();

        expect($client->query('{ shop { id } }'))->toBe(['ok' => 1])
            ->and($sleeps->getArrayCopy())->toBe([3]);
    });

    it('desiste do throttle apos maxRetries e lanca a excecao marcada como throttled', function () {
        Http::fake(['*/graphql.json' => Http::response([
            'errors' => [['message' => 'Throttled', 'extensions' => ['code' => 'THROTTLED']]],
        ], 200)]);

        $client = (new GraphQLClient(GraphQLClient::make(shopifyGqlClientIntegration()), shopifyGqlClientIntegration(), maxRetries: 1))
            ->withSleeper(fn (int $s) => null);

        try {
            $client->query('{ shop { id } }');
            $this->fail('deveria lancar');
        } catch (ShopifyGraphQLException $e) {
            expect($e->isThrottled())->toBeTrue();
        }
        Http::assertSentCount(2);
    });

    it('paginate segue pageInfo.hasNextPage/endCursor injetando a variavel after', function () {
        Http::fake(['*/graphql.json' => Http::sequence()
            ->push(['data' => ['orders' => ['edges' => [['node' => ['id' => 'a']], ['node' => ['id' => 'b']]], 'pageInfo' => ['hasNextPage' => true, 'endCursor' => 'CUR2']]]], 200)
            ->push(['data' => ['orders' => ['edges' => [['node' => ['id' => 'c']]], 'pageInfo' => ['hasNextPage' => false, 'endCursor' => 'CUR3']]]], 200)]);

        $doc = 'query orders($first: Int, $after: String) { orders(first: $first, after: $after) { edges { node { id } } pageInfo { hasNextPage endCursor } } }';
        $nodes = iterator_to_array(GraphQLClient::forIntegration(shopifyGqlClientIntegration())->paginate($doc, ['first' => 2], 'orders'), false);

        expect(array_column($nodes, 'id'))->toBe(['a', 'b', 'c']);
        Http::assertSentCount(2);
        Http::assertSent(fn (Request $r) => (json_decode($r->body(), true)['variables']['after'] ?? null) === 'CUR2');
    });

    it('paginate aceita caminho aninhado (product.variants)', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['product' => ['variants' => [
            'edges' => [['node' => ['id' => 'v1']]], 'pageInfo' => ['hasNextPage' => false, 'endCursor' => null],
        ]]]], 200)]);

        $nodes = iterator_to_array(GraphQLClient::forIntegration(shopifyGqlClientIntegration())->paginate('q', [], 'product.variants'), false);

        expect($nodes)->toBe([['id' => 'v1']]);
    });
});

describe('Shopify GraphQL BaseOperations::document', function () {
    it('declara somente as variaveis presentes em args, com os tipos do schema', function () {
        $doc = BaseOperations::document('query', 'orders', ['first' => 10, 'query' => 'x'],
            ['first' => 'Int', 'after' => 'String', 'query' => 'String'], 'edges { node { id } }');

        expect($doc)->toBe('query orders($first: Int, $query: String) { orders(first: $first, query: $query) { edges { node { id } } } }');
    });

    it('sem args nem selection vira chamada simples', function () {
        expect(BaseOperations::document('query', 'articleTags', [], ['limit' => 'Int!'], ''))
            ->toBe('query articleTags { articleTags }');
    });

    it('rejeita argumento desconhecido', function () {
        expect(fn () => BaseOperations::document('query', 'order', ['foo' => 1], ['id' => 'ID!'], 'id'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('metodo gerado aceita selection propria', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['order' => ['id' => 'gid://shopify/Order/1']]], 200)]);

        $order = (new OrderQueries(GraphQLClient::forIntegration(shopifyGqlClientIntegration())))
            ->order(['id' => 'gid://shopify/Order/1'], 'id lineItems(first: 5) { edges { node { id } } }');

        expect($order['id'])->toBe('gid://shopify/Order/1');
        Http::assertSent(fn (Request $r) => json_decode($r->body(), true)['query'] === 'query order($id: ID!) { order(id: $id) { id lineItems(first: 5) { edges { node { id } } } } }');
    });
});

describe('Shopify GraphQL Admin hub', function () {
    it('expoe client, queries e mutations por dominio', function () {
        $admin = Admin::forIntegration(shopifyGqlClientIntegration());

        expect($admin->client())->toBeInstanceOf(GraphQLClient::class)
            ->and($admin->queries()->orders())->toBeInstanceOf(OrderQueries::class)
            ->and($admin->mutations()->orders())->toBeInstanceOf(OrderMutations::class);
    });
});
