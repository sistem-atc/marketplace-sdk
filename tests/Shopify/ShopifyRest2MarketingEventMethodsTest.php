<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Marketing\MarketingEventMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify MarketingEventMethods', function () {
    it('list', fn () => shopifyRest2Call(fn () => shopifyRest2Make(MarketingEventMethods::class)->list(['limit' => 10, 'offset' => 20]), 'GET', 'marketing_events.json?limit=10&offset=20'));
    it('count', fn () => shopifyRest2Call(fn () => shopifyRest2Make(MarketingEventMethods::class)->count(), 'GET', 'marketing_events/count.json', null, ['count' => 1]));
    it('get', fn () => shopifyRest2Call(fn () => shopifyRest2Make(MarketingEventMethods::class)->get(4), 'GET', 'marketing_events/4.json'));
    it('create embrulha em marketing_event', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(MarketingEventMethods::class)->create(['event_type' => 'ad', 'marketing_channel' => 'social']),
        'POST', 'marketing_events.json', ['marketing_event' => ['event_type' => 'ad', 'marketing_channel' => 'social']],
    ));
    it('update embrulha em marketing_event', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(MarketingEventMethods::class)->update(4, ['budget' => '10']),
        'PUT', 'marketing_events/4.json', ['marketing_event' => ['budget' => '10']],
    ));
    it('delete', fn () => shopifyRest2Call(fn () => shopifyRest2Make(MarketingEventMethods::class)->delete(4), 'DELETE', 'marketing_events/4.json'));
    it('engagements embrulha em engagements', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(MarketingEventMethods::class)->engagements(4, [['occurred_on' => '2026-09-01', 'clicks_count' => 3]]),
        'POST', 'marketing_events/4/engagements.json', ['engagements' => [['occurred_on' => '2026-09-01', 'clicks_count' => 3]]],
    ));
});
