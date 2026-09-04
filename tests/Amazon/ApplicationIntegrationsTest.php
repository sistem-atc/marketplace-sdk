<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\ApplicationIntegrations;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

const APPINT = 'https://sellingpartnerapi-na.amazon.com/appIntegrations/2024-04-01';

beforeEach(function () {
    Http::preventStrayRequests();
});

function applicationIntegrationsEndpoint(): ApplicationIntegrations
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new ApplicationIntegrations(new Client($integration));
}

function applicationIntegrationsAssertSent(string $url, array $body): void
{
    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && $r->url() === $url
        && $r->header('x-amz-access-token')[0] === 'Atza|valid-token'
        && $r->data() === $body);
}

it('createNotification POST /notifications com templateId + notificationParameters (token de seller)', function () {
    Http::fake([APPINT.'/notifications' => Http::response(['notificationId' => 'NT-1'])]);
    $body = ['templateId' => 'TPL-1', 'notificationParameters' => ['orderId' => '123'], 'marketplaceId' => 'A2Q3Y263D00KWC'];
    expect(applicationIntegrationsEndpoint()->createNotification($body)['notificationId'])->toBe('NT-1');
    applicationIntegrationsAssertSent(APPINT.'/notifications', $body);
});

it('deleteNotifications POST /notifications/deletion', function () {
    Http::fake([APPINT.'/notifications/deletion' => Http::response('', 204)]);
    $body = ['templateId' => 'TPL-1', 'deletionReason' => 'INCORRECT_CONTENT'];
    expect(applicationIntegrationsEndpoint()->deleteNotifications($body))->toBe([]);
    applicationIntegrationsAssertSent(APPINT.'/notifications/deletion', $body);
});

it('recordActionFeedback POST /notifications/{id}/feedback', function () {
    Http::fake([APPINT.'/notifications/NT-1/feedback' => Http::response('', 204)]);
    $body = ['feedbackActionCode' => 'SELLER_ACTION_COMPLETED'];
    expect(applicationIntegrationsEndpoint()->recordActionFeedback('NT-1', $body))->toBe([]);
    applicationIntegrationsAssertSent(APPINT.'/notifications/NT-1/feedback', $body);
});
