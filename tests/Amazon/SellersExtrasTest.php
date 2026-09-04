<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Sellers;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function sellersExtras(): Sellers
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new Sellers(new Client($integration));
}

/** Um único request enviado, com verbo + URL exata + token LWA. */
function sellersExtrasAssertSent(string $method, string $url, ?array $body = null, string $token = 'Atza|valid-token'): void
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

it('getAccount GET /sellers/v1/account devolve o JSON inteiro (payload)', function () {
    Http::fake(['*/sellers/v1/account' => Http::response(['payload' => ['businessType' => 'PRIVATE_LIMITED', 'sellingPlan' => 'PROFESSIONAL']])]);
    expect(sellersExtras()->getAccount()['payload']['sellingPlan'])->toBe('PROFESSIONAL');
    sellersExtrasAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/sellers/v1/account');
});
