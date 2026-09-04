<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

/*
 * Helpers globais dos testes da Loja Integrada (prefixo lojaIntegrada*).
 * Carregado via require_once em cada *Test.php da pasta.
 */

if (! function_exists('lojaIntegradaIntegration')) {
    function lojaIntegradaIntegration(array $settings = []): FakeIntegration
    {
        return new FakeIntegration(
            accessToken: null,
            refreshToken: null,
            settings: $settings ?: ['api_key' => 'k1', 'application_key' => 'a1'],
            active: true,
            expired: false,
        );
    }

    /** Instancia um grupo de endpoints com o client da factory (sem passar pelo root). */
    function lojaIntegradaMethods(string $class, ?FakeIntegration $integration = null): object
    {
        $integration ??= lojaIntegradaIntegration();

        return new $class(HttpClientFactory::make($integration), $integration);
    }

    function lojaIntegradaFake(array $response = ['ok' => true], int $status = 200): void
    {
        Http::preventStrayRequests();
        Http::fake(['api.awsli.com.br/*' => Http::response($response, $status)]);
    }

    function lojaIntegradaAuthHeader(): string
    {
        return 'chave_api k1 aplicacao a1';
    }

    /** Body da request como array associativo (json ou multipart name=>contents). */
    function lojaIntegradaBody(Request $req): array
    {
        $data = $req->data();
        if ($req->isMultipart()) {
            $out = [];
            foreach ($data as $part) {
                $out[$part['name']] = $part['contents'];
            }

            return $out;
        }

        return is_array($data) ? $data : [];
    }

    /**
     * Garante: 1 request, verbo, URL COMPLETA (com query), header Authorization,
     * body (subset exato das chaves informadas) e headers extras.
     */
    function lojaIntegradaAssertSent(string $method, string $url, ?array $body = null, array $headers = [], bool $multipart = false): void
    {
        Http::assertSentCount(1);
        Http::assertSent(function (Request $req) use ($method, $url, $body, $headers, $multipart) {
            expect($req->method())->toBe($method);
            expect($req->url())->toBe($url);
            expect($req->hasHeader('Authorization', lojaIntegradaAuthHeader()))->toBeTrue();
            foreach ($headers as $name => $value) {
                expect($req->hasHeader($name, $value))->toBeTrue("header {$name}");
            }
            if ($multipart) {
                expect($req->isMultipart())->toBeTrue();
            }
            if ($body !== null) {
                $sent = lojaIntegradaBody($req);
                foreach ($body as $k => $v) {
                    expect($sent)->toHaveKey($k);
                    expect($sent[$k])->toEqual($v);
                }
            } elseif ($method !== 'GET') {
                expect(lojaIntegradaBody($req))->toBe([]);
            }

            return true;
        });
    }
}
