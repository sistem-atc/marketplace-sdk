<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\MobileApp\MobilePlatformApplicationMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify MobilePlatformApplicationMethods', function () {
    it('list', fn () => shopifyRest2Call(fn () => shopifyRest2Make(MobilePlatformApplicationMethods::class)->list(), 'GET', 'mobile_platform_applications.json'));
    it('get', fn () => shopifyRest2Call(fn () => shopifyRest2Make(MobilePlatformApplicationMethods::class)->get(2), 'GET', 'mobile_platform_applications/2.json'));
    it('create embrulha', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(MobilePlatformApplicationMethods::class)->create(['platform' => 'ios', 'application_id' => 'X.com.app']),
        'POST', 'mobile_platform_applications.json', ['mobile_platform_application' => ['platform' => 'ios', 'application_id' => 'X.com.app']],
    ));
    it('update embrulha', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(MobilePlatformApplicationMethods::class)->update(2, ['enabled_universal_or_app_links' => true]),
        'PUT', 'mobile_platform_applications/2.json', ['mobile_platform_application' => ['enabled_universal_or_app_links' => true]],
    ));
    it('delete', fn () => shopifyRest2Call(fn () => shopifyRest2Make(MobilePlatformApplicationMethods::class)->delete(2), 'DELETE', 'mobile_platform_applications/2.json'));
});
