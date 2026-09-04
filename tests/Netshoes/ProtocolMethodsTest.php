<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Netshoes\Endpoints\Protocol\ProtocolMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

const NETSHOES_PROTOCOL_BASE = 'https://api-marketplace.netshoes.com.br';

beforeEach(function () {
    Http::preventStrayRequests();
    config(['marketplaces.netshoes.api_base' => NETSHOES_PROTOCOL_BASE]);
});

function netshoesProtocol(): ProtocolMethods
{
    return MarketPlaces::Netshoes()->protocols(
        new FakeIntegration(accessToken: 'tok-p', settings: ['client_id' => 'cli-p']),
    );
}

function netshoesProtocolSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn (Request $req) => $req->method() === $method
        && urldecode($req->url()) === $url
        && $req->hasHeader('client_id', 'cli-p')
        && $req->hasHeader('access_token', 'tok-p')
        && ($body === null || $req->data() === $body));
}

describe('Protocols — /api/v1/protocols', function () {
    it('listProtocols: GET com expand', function () {
        Http::fake(['*' => Http::response(['items' => []])]);

        netshoesProtocol()->listProtocols(['messages']);

        netshoesProtocolSent('GET', NETSHOES_PROTOCOL_BASE.'/api/v1/protocols?expand=messages');
    });

    it('listProtocols: sem expand nao manda query', function () {
        Http::fake(['*' => Http::response(['items' => []])]);

        netshoesProtocol()->listProtocols();

        netshoesProtocolSent('GET', NETSHOES_PROTOCOL_BASE.'/api/v1/protocols');
    });

    it('getProtocol: GET /protocols/{n} com rawurlencode', function () {
        Http::fake(['*' => Http::response(['protocolNumber' => 'P 1'])]);

        netshoesProtocol()->getProtocol('P 1');

        Http::assertSent(fn (Request $req) => $req->url() === NETSHOES_PROTOCOL_BASE.'/api/v1/protocols/P%201');
    });

    it('listProtocolMessages: GET /protocols/{n}/messages', function () {
        Http::fake(['*' => Http::response(['items' => []])]);

        netshoesProtocol()->listProtocolMessages('P1');

        netshoesProtocolSent('GET', NETSHOES_PROTOCOL_BASE.'/api/v1/protocols/P1/messages');
    });

    it('postProtocolMessage: POST /protocols/{n}/messages com PostMessageResource e expand', function () {
        Http::fake(['*' => Http::response(['protocolNumber' => 'P1'])]);

        $msg = ['text' => 'Enviado hoje', 'responsible' => 'Soldiers', 'messageDate' => '2026-09-03T12:00:00Z'];
        netshoesProtocol()->postProtocolMessage('P1', $msg, ['messages']);

        netshoesProtocolSent('POST', NETSHOES_PROTOCOL_BASE.'/api/v1/protocols/P1/messages?expand=messages', $msg);
    });

    it('listOrderProtocols: GET /orders/{n}/protocols', function () {
        Http::fake(['*' => Http::response(['items' => []])]);

        netshoesProtocol()->listOrderProtocols('123', ['messages']);

        netshoesProtocolSent('GET', NETSHOES_PROTOCOL_BASE.'/api/v1/orders/123/protocols?expand=messages');
    });

    it('getOrderProtocol: GET /orders/{n}/protocols/{p}', function () {
        Http::fake(['*' => Http::response(['protocolNumber' => 'P1'])]);

        netshoesProtocol()->getOrderProtocol('123', 'P1');

        netshoesProtocolSent('GET', NETSHOES_PROTOCOL_BASE.'/api/v1/orders/123/protocols/P1');
    });

    it('listOrderProtocolMessages: GET /orders/{n}/protocols/{p}/messages', function () {
        Http::fake(['*' => Http::response(['items' => []])]);

        netshoesProtocol()->listOrderProtocolMessages('123', 'P1');

        netshoesProtocolSent('GET', NETSHOES_PROTOCOL_BASE.'/api/v1/orders/123/protocols/P1/messages');
    });
});
