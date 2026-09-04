<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Messaging;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function messagingExtras(): Messaging
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new Messaging(new Client($integration));
}

/** Um único request enviado, com verbo + URL exata + token LWA. */
function messagingExtrasAssertSent(string $method, string $url, ?array $body = null, string $token = 'Atza|valid-token'): void
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

const MSG = 'https://sellingpartnerapi-na.amazon.com/messaging/v1/orders/701-1111111-1111111';

it('getMessagingActionsForOrder GET /messaging/v1/orders/{id}?marketplaceIds=csv', function () {
    Http::fake([MSG.'?*' => Http::response(['_links' => ['actions' => []]])]);
    messagingExtras()->getMessagingActionsForOrder('701-1111111-1111111', ['A2Q3Y263D00KWC', 'ATVPDKIKX0DER']);
    messagingExtrasAssertSent('GET', MSG.'?marketplaceIds=A2Q3Y263D00KWC%2CATVPDKIKX0DER');
});

dataset('messaging_text_messages', [
    'confirmDeliveryDetails' => ['createConfirmDeliveryDetails', 'confirmDeliveryDetails'],
    'confirmOrderDetails' => ['createConfirmOrderDetails', 'confirmOrderDetails'],
    'confirmServiceDetails' => ['createConfirmServiceDetails', 'confirmServiceDetails'],
    'unexpectedProblem' => ['createUnexpectedProblem', 'unexpectedProblem'],
    'digitalAccessKey' => ['createDigitalAccessKey', 'digitalAccessKey'],
]);

it('mensagens de texto POST /messages/{type}?marketplaceIds= com body', function (string $method, string $type) {
    Http::fake([MSG.'/messages/'.$type.'*' => Http::response([], 201)]);
    messagingExtras()->{$method}('701-1111111-1111111', ['A2Q3Y263D00KWC'], ['text' => 'Olá']);
    messagingExtrasAssertSent('POST', MSG.'/messages/'.$type.'?marketplaceIds=A2Q3Y263D00KWC', ['text' => 'Olá']);
})->with('messaging_text_messages');

it('createLegalDisclosure POST /messages/legalDisclosure com attachments', function () {
    Http::fake([MSG.'/messages/legalDisclosure*' => Http::response([], 201)]);
    $body = ['attachments' => [['uploadDestinationId' => 'U1', 'fileName' => 'termo.pdf']]];
    messagingExtras()->createLegalDisclosure('701-1111111-1111111', ['A2Q3Y263D00KWC'], $body);
    messagingExtrasAssertSent('POST', MSG.'/messages/legalDisclosure?marketplaceIds=A2Q3Y263D00KWC', $body);
});

it('sendInvoice POST /messages/invoice com o PDF ja enviado pela Uploads API', function () {
    Http::fake([MSG.'/messages/invoice*' => Http::response([], 201)]);
    $body = ['attachments' => [['uploadDestinationId' => 'U2', 'fileName' => 'danfe.pdf']]];
    messagingExtras()->sendInvoice('701-1111111-1111111', ['A2Q3Y263D00KWC'], $body);
    messagingExtrasAssertSent('POST', MSG.'/messages/invoice?marketplaceIds=A2Q3Y263D00KWC', $body);
});
