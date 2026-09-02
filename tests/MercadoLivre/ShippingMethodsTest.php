<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Shipping\ShippingMethods;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shippingTestIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function shippingTestMethods(): ShippingMethods
{
    $integration = shippingTestIntegration();

    return new ShippingMethods(HttpClientFactory::make($integration), $integration);
}

/** Le um campo simples de um request multipart (data() vira lista de partes {name, contents}). */
function shippingTestMultipartField(\Illuminate\Http\Client\Request $req, string $name): ?string
{
    foreach ($req->data() as $part) {
        if (($part['name'] ?? null) === $name) return (string) $part['contents'];
    }

    return null;
}

beforeEach(function () {
    config([
        'marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.access_token_ttl_seconds' => 21600,
        'mercadolivre.default_site_id' => 'MLB',
    ]);
    Http::preventStrayRequests();
});

describe('ShippingMethods modos e preferencias', function () {
    it('siteShippingMethods: GET /sites/{site}/shipping_methods', function () {
        Http::fake(['api.mercadolibre.com/sites/MLB/shipping_methods' => Http::response([['id' => 73328, 'name' => 'Normal']])]);

        expect(shippingTestMethods()->siteShippingMethods('MLB')[0]['id'])->toBe(73328);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/sites/MLB/shipping_methods'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('userShippingPreferences: GET /users/{id}/shipping_preferences', function () {
        Http::fake(['api.mercadolibre.com/users/64196652/shipping_preferences' => Http::response(['modes' => ['me2'], 'tags' => ['proximity']])]);

        expect(shippingTestMethods()->userShippingPreferences(64196652)['tags'])->toBe(['proximity']);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/users/64196652/shipping_preferences'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('categoryShippingPreferences: GET /categories/{id}/shipping_preferences', function () {
        Http::fake(['api.mercadolibre.com/categories/MLB438794/shipping_preferences' => Http::response(['logistics' => []])]);

        shippingTestMethods()->categoryShippingPreferences('MLB438794');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/categories/MLB438794/shipping_preferences');
    });

    it('domainShippingAttributes: GET /catalog_domains/{domain}/shipping_attributes', function () {
        Http::fake(['api.mercadolibre.com/catalog_domains/MLB-AUTOMOTIVE_TIRES/shipping_attributes' => Http::response(['domain_id' => 'MLB-AUTOMOTIVE_TIRES'])]);

        expect(shippingTestMethods()->domainShippingAttributes('MLB-AUTOMOTIVE_TIRES')['domain_id'])->toBe('MLB-AUTOMOTIVE_TIRES');
        Http::assertSent(fn ($req) => $req->url() === 'https://api.mercadolibre.com/catalog_domains/MLB-AUTOMOTIVE_TIRES/shipping_attributes');
    });

    it('itemShippingOptions: GET /items/{id}/shipping_options?zip_code=', function () {
        Http::fake(['api.mercadolibre.com/items/MLB1398714241/shipping_options*' => Http::response(['options' => []])]);

        shippingTestMethods()->itemShippingOptions('MLB1398714241', ['zip_code' => '01310100']);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/items/MLB1398714241/shipping_options?zip_code=01310100'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('freeShippingOptions: GET /users/{id}/shipping_options/free com query', function () {
        Http::fake(['api.mercadolibre.com/users/244878077/shipping_options/free*' => Http::response(['coverage' => []])]);

        shippingTestMethods()->freeShippingOptions(244878077, ['dimensions' => '9x17x22,462', 'item_price' => 300, 'free_shipping' => 'true']);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && str_starts_with($req->url(), 'https://api.mercadolibre.com/users/244878077/shipping_options/free?')
            && str_contains($req->url(), 'dimensions=9x17x22%2C462')
            && str_contains($req->url(), 'item_price=300')
            && str_contains($req->url(), 'free_shipping=true'));
    });

    it('validateShippingModes: POST /users/{id}/shipping_modes com x-multichannel + X-Format-New', function () {
        Http::fake(['api.mercadolibre.com/users/123/shipping_modes' => Http::response(['shipping' => ['mode' => 'me2']])]);

        $body = ['site_id' => 'MLB', 'item_id' => 'MLB3856335025', 'category_id' => 'MLB1626'];
        shippingTestMethods()->validateShippingModes(123, $body);
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/users/123/shipping_modes'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer')
            && $req->hasHeader('x-multichannel', 'true')
            && $req->hasHeader('X-Format-New', 'true')
            && $req['item_id'] === 'MLB3856335025');
    });

    it('shippabilityServices: GET .../contracts/shippability/services?legacy_attributes=true', function () {
        Http::fake(['api.mercadolibre.com/customers/marketplace/sites/MLA/user-products/MLAU1234567890/contracts/shippability/services*' => Http::response(['services' => []])]);

        shippingTestMethods()->shippabilityServices('MLA', 'MLAU1234567890', true);
        shippingTestMethods()->shippabilityServices('MLA', 'MLAU1234567890');

        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/customers/marketplace/sites/MLA/user-products/MLAU1234567890/contracts/shippability/services?legacy_attributes=true'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
        Http::assertSent(fn ($req) => $req->url() === 'https://api.mercadolibre.com/customers/marketplace/sites/MLA/user-products/MLAU1234567890/contracts/shippability/services');
    });
});

describe('ShippingMethods ME1 frete dinamico', function () {
    it('me1Metrics: GET /shipping/me1/sites/{site}/metrics com ts_from/ts_to/seller_id', function () {
        Http::fake(['api.mercadolibre.com/shipping/me1/sites/MLB/metrics*' => Http::response(['summary' => []])]);

        shippingTestMethods()->me1Metrics('MLB', '2023-10-01T00:00:00Z', '2023-10-31T23:59:59Z', 123456789);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && str_starts_with($req->url(), 'https://api.mercadolibre.com/shipping/me1/sites/MLB/metrics?')
            && str_contains($req->url(), 'ts_from=2023-10-01T00%3A00%3A00Z')
            && str_contains($req->url(), 'ts_to=2023-10-31T23%3A59%3A59Z')
            && str_contains($req->url(), 'seller_id=123456789')
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('me1SimulateQuotation: POST /shipping/me1/v1/quotation/simulate', function () {
        Http::fake(['api.mercadolibre.com/shipping/me1/v1/quotation/simulate' => Http::response(['quotations' => []])]);

        shippingTestMethods()->me1SimulateQuotation(150.0, ['width' => 20.0, 'height' => 15.0, 'length' => 30.0, 'weight' => 1000], ['type' => 'zipcode', 'value' => '01310100']);
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/shipping/me1/v1/quotation/simulate'
            && $req['declared_value'] === 150.0
            && $req['destination'] === ['type' => 'zipcode', 'value' => '01310100']
            && $req['dimensions']['weight'] === 1000);
    });

    it('me1TariffTemplate: GET /shipping/me1/v1/tariff/template?site=', function () {
        Http::fake(['api.mercadolibre.com/shipping/me1/v1/tariff/template*' => Http::response(['filename' => 'tariff_template_MLB.xlsx'])]);

        expect(shippingTestMethods()->me1TariffTemplate('MLB')['filename'])->toBe('tariff_template_MLB.xlsx');
        Http::assertSent(fn ($req) => $req->url() === 'https://api.mercadolibre.com/shipping/me1/v1/tariff/template?site=MLB');
    });

    it('me1Tariff: GET /shipping/me1/v1/tariff/{uuid}', function () {
        Http::fake(['api.mercadolibre.com/shipping/me1/v1/tariff/550e8400-e29b-41d4-a716-446655440000' => Http::response(['status' => 'active'])]);

        expect(shippingTestMethods()->me1Tariff('550e8400-e29b-41d4-a716-446655440000')['status'])->toBe('active');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/shipping/me1/v1/tariff/550e8400-e29b-41d4-a716-446655440000'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('me1UpdateTariff: POST multipart /shipping/me1/v1/tariff/update', function () {
        Http::fake(['api.mercadolibre.com/shipping/me1/v1/tariff/update' => Http::response(['resource_id' => 'daba7e52', 'status' => 'accepted'], 202)]);

        $out = shippingTestMethods()->me1UpdateTariff('MLB', 'transportadora-17', 'xlsx-bytes', 'tariff_table.xlsx', 'https://example.com/webhook');

        expect($out['status'])->toBe('accepted');
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/shipping/me1/v1/tariff/update'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer')
            && $req->isMultipart()
            && $req->hasFile('file', 'xlsx-bytes', 'tariff_table.xlsx')
            && shippingTestMultipartField($req, 'site') === 'MLB'
            && shippingTestMultipartField($req, 'service') === 'transportadora-17'
            && shippingTestMultipartField($req, 'callback_url') === 'https://example.com/webhook');
    });

    it('me1UpdateTariff: erro HTTP lanca MercadoLivreRequestException', function () {
        Http::fake(['api.mercadolibre.com/shipping/me1/v1/tariff/update' => Http::response(['error' => 'bad'], 400)]);

        shippingTestMethods()->me1UpdateTariff('MLB', 'svc', 'bytes');
    })->throws(MercadoLivreRequestException::class);
});

describe('ShippingMethods fulfillment operations', function () {
    it('fulfillmentOperationsSearch: GET /stock/fulfillment/operations/search com scroll', function () {
        Http::fake(['api.mercadolibre.com/stock/fulfillment/operations/search*' => Http::response(['paging' => ['total' => 4, 'scroll' => null], 'results' => []])]);

        shippingTestMethods()->fulfillmentOperationsSearch(['seller_id' => 384324657, 'inventory_id' => 'DEHW09303', 'date_from' => '2020-06-01', 'date_to' => '2020-06-30', 'scroll' => 'abc']);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && str_starts_with($req->url(), 'https://api.mercadolibre.com/stock/fulfillment/operations/search?')
            && str_contains($req->url(), 'seller_id=384324657')
            && str_contains($req->url(), 'inventory_id=DEHW09303')
            && str_contains($req->url(), 'date_from=2020-06-01')
            && str_contains($req->url(), 'scroll=abc')
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('fulfillmentOperation: GET /stock/fulfillment/operations/{id}', function () {
        Http::fake(['api.mercadolibre.com/stock/fulfillment/operations/329663159' => Http::response(['id' => '329663159', 'type' => 'SALE_CONFIRMATION'])]);

        expect(shippingTestMethods()->fulfillmentOperation(329663159)['type'])->toBe('SALE_CONFIRMATION');
        Http::assertSent(fn ($req) => $req->url() === 'https://api.mercadolibre.com/stock/fulfillment/operations/329663159');
    });
});
