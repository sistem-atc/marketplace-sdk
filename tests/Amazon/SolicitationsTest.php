<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Solicitations;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function solicitations(): Solicitations
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new Solicitations(new Client($integration));
}

/** Um único request enviado, com verbo + URL exata + token LWA. */
function solicitationsAssertSent(string $method, string $url, ?array $body = null, string $token = 'Atza|valid-token'): void
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

const SOL = 'https://sellingpartnerapi-na.amazon.com/solicitations/v1/orders/701-1111111-1111111';

it('getSolicitationActionsForOrder GET /solicitations/v1/orders/{id}?marketplaceIds=csv', function () {
    Http::fake([SOL.'?*' => Http::response(['_links' => ['actions' => [['name' => 'productReviewAndSellerFeedback']]]])]);
    expect(solicitations()->getSolicitationActionsForOrder('701-1111111-1111111', ['A2Q3Y263D00KWC'])['_links']['actions'][0]['name'])->toBe('productReviewAndSellerFeedback');
    solicitationsAssertSent('GET', SOL.'?marketplaceIds=A2Q3Y263D00KWC');
});

it('createProductReviewAndSellerFeedbackSolicitation POST sem body com marketplaceIds na query', function () {
    Http::fake([SOL.'/solicitations/productReviewAndSellerFeedback*' => Http::response([], 201)]);
    solicitations()->createProductReviewAndSellerFeedbackSolicitation('701-1111111-1111111', ['A2Q3Y263D00KWC', 'ATVPDKIKX0DER']);
    solicitationsAssertSent('POST', SOL.'/solicitations/productReviewAndSellerFeedback?marketplaceIds=A2Q3Y263D00KWC%2CATVPDKIKX0DER', []);
});
