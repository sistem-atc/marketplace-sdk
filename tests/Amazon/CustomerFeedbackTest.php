<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\CustomerFeedback;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function customerFeedback(): CustomerFeedback
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new CustomerFeedback(new Client($integration));
}

/** Um único request enviado, com verbo + URL exata + token LWA. */
function customerFeedbackAssertSent(string $method, string $url, ?array $body = null, string $token = 'Atza|valid-token'): void
{
    Http::assertSent(function (Request $req) use ($method, $url, $body, $token): bool {
        if ($req->method() !== $method || $req->url() !== $url) {
            return false;
        }
        if ($req->header('x-amz-access-token')[0] !== $token) {
            return false;
        }
        if ($body !== null && $req->data() !== $body) {
            return false;
        }

        return true;
    });
}

const CF = 'https://sellingpartnerapi-na.amazon.com/customerFeedback/2024-06-01';

it('getItemReviewTopics GET /items/{asin}/reviews/topics com sortBy', function () {
    Http::fake([CF.'/items/B0ABC/reviews/topics*' => Http::response(['asin' => 'B0ABC', 'topics' => []])]);
    expect(customerFeedback()->getItemReviewTopics('B0ABC', 'A2Q3Y263D00KWC', 'STAR_RATING_IMPACT')['asin'])->toBe('B0ABC');
    customerFeedbackAssertSent('GET', CF.'/items/B0ABC/reviews/topics?marketplaceId=A2Q3Y263D00KWC&sortBy=STAR_RATING_IMPACT');
});

it('getItemBrowseNode GET /items/{asin}/browseNode', function () {
    Http::fake([CF.'/items/B0ABC/browseNode*' => Http::response(['browseNodeId' => '123', 'displayName' => 'Whey'])]);
    expect(customerFeedback()->getItemBrowseNode('B0ABC', 'A2Q3Y263D00KWC')['browseNodeId'])->toBe('123');
    customerFeedbackAssertSent('GET', CF.'/items/B0ABC/browseNode?marketplaceId=A2Q3Y263D00KWC');
});

it('getBrowseNodeReviewTopics GET /browseNodes/{id}/reviews/topics (sortBy default MENTIONS)', function () {
    Http::fake([CF.'/browseNodes/123/reviews/topics*' => Http::response(['topics' => []])]);
    customerFeedback()->getBrowseNodeReviewTopics('123', 'A2Q3Y263D00KWC');
    customerFeedbackAssertSent('GET', CF.'/browseNodes/123/reviews/topics?marketplaceId=A2Q3Y263D00KWC&sortBy=MENTIONS');
});

it('getItemReviewTrends GET /items/{asin}/reviews/trends', function () {
    Http::fake([CF.'/items/B0ABC/reviews/trends*' => Http::response(['reviewTrends' => []])]);
    customerFeedback()->getItemReviewTrends('B0ABC', 'A2Q3Y263D00KWC');
    customerFeedbackAssertSent('GET', CF.'/items/B0ABC/reviews/trends?marketplaceId=A2Q3Y263D00KWC');
});

it('getBrowseNodeReviewTrends GET /browseNodes/{id}/reviews/trends', function () {
    Http::fake([CF.'/browseNodes/123/reviews/trends*' => Http::response(['reviewTrends' => []])]);
    customerFeedback()->getBrowseNodeReviewTrends('123', 'A2Q3Y263D00KWC');
    customerFeedbackAssertSent('GET', CF.'/browseNodes/123/reviews/trends?marketplaceId=A2Q3Y263D00KWC');
});

it('getBrowseNodeReturnTopics GET /browseNodes/{id}/returns/topics', function () {
    Http::fake([CF.'/browseNodes/123/returns/topics*' => Http::response(['topics' => []])]);
    customerFeedback()->getBrowseNodeReturnTopics('123', 'A2Q3Y263D00KWC');
    customerFeedbackAssertSent('GET', CF.'/browseNodes/123/returns/topics?marketplaceId=A2Q3Y263D00KWC');
});

it('getBrowseNodeReturnTrends GET /browseNodes/{id}/returns/trends', function () {
    Http::fake([CF.'/browseNodes/123/returns/trends*' => Http::response(['returnTrends' => []])]);
    customerFeedback()->getBrowseNodeReturnTrends('123', 'A2Q3Y263D00KWC');
    customerFeedbackAssertSent('GET', CF.'/browseNodes/123/returns/trends?marketplaceId=A2Q3Y263D00KWC');
});
