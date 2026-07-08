<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreAuthenticationException;
use SistemAtc\Marketplaces\MercadoLivre\Support\OAuth;

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('OAuth::generatePkce', function () {
    it('gera verifier/challenge base64url validos e derivados por S256', function () {
        $pkce = OAuth::generatePkce();

        expect($pkce)->toHaveKeys(['verifier', 'challenge']);
        // base64url: sem +, /, =
        expect($pkce['verifier'])->toMatch('/^[A-Za-z0-9\-_]+$/');
        expect($pkce['challenge'])->toMatch('/^[A-Za-z0-9\-_]+$/');

        // challenge = base64url(sha256(verifier))
        $expected = rtrim(strtr(base64_encode(hash('sha256', $pkce['verifier'], true)), '+/', '-_'), '=');
        expect($pkce['challenge'])->toBe($expected);
    });

    it('gera pares distintos a cada chamada', function () {
        expect(OAuth::generatePkce()['verifier'])
            ->not->toBe(OAuth::generatePkce()['verifier']);
    });
});

describe('OAuth::authorizationUrl', function () {
    it('monta a URL de autorizacao BR com response_type=code, state e PKCE S256', function () {
        $url = OAuth::authorizationUrl(
            clientId: 'app-123',
            redirectUri: 'https://bunker.test/oauth/callback/mercado-livre',
            state: '9|abc',
            codeChallenge: 'CHAL',
        );

        expect($url)->toStartWith('https://auth.mercadolivre.com.br/authorization?');
        parse_str(parse_url($url, PHP_URL_QUERY), $q);
        expect($q['response_type'])->toBe('code');
        expect($q['client_id'])->toBe('app-123');
        expect($q['redirect_uri'])->toBe('https://bunker.test/oauth/callback/mercado-livre');
        expect($q['state'])->toBe('9|abc');
        expect($q['code_challenge'])->toBe('CHAL');
        expect($q['code_challenge_method'])->toBe('S256');
    });

    it('omite os campos PKCE quando nao ha challenge', function () {
        $url = OAuth::authorizationUrl('app', 'https://x/cb', 'st');
        expect($url)->not->toContain('code_challenge');
    });
});

describe('OAuth::exchangeAuthorizationCode', function () {
    it('troca o code por tokens enviando code_verifier (PKCE)', function () {
        Http::fake([
            'api.mercadolibre.com/oauth/token' => Http::response([
                'access_token'  => 'AT',
                'refresh_token' => 'RT',
                'expires_in'    => 21600,
            ], 200),
        ]);

        $tokens = OAuth::exchangeAuthorizationCode(
            clientId: 'cli',
            clientSecret: 'sec',
            code: 'CODE',
            redirectUri: 'https://x/cb',
            codeVerifier: 'VER',
        );

        expect($tokens)->toBe([
            'access_token'  => 'AT',
            'refresh_token' => 'RT',
            'expires_in'    => 21600,
        ]);

        Http::assertSent(function ($req) {
            parse_str($req->body(), $b);

            return $b['grant_type'] === 'authorization_code'
                && $b['client_id'] === 'cli'
                && $b['client_secret'] === 'sec'
                && $b['code'] === 'CODE'
                && $b['redirect_uri'] === 'https://x/cb'
                && $b['code_verifier'] === 'VER';
        });
    });

    it('lanca quando a resposta nao tem refresh_token (falta offline_access)', function () {
        Http::fake([
            'api.mercadolibre.com/oauth/token' => Http::response([
                'access_token' => 'AT',
                'expires_in'   => 21600,
            ], 200),
        ]);

        OAuth::exchangeAuthorizationCode('cli', 'sec', 'CODE', 'https://x/cb', 'VER');
    })->throws(MercadoLivreAuthenticationException::class, 'offline_access');

    it('lanca em falha HTTP', function () {
        Http::fake([
            'api.mercadolibre.com/oauth/token' => Http::response(['message' => 'invalid_grant'], 400),
        ]);

        OAuth::exchangeAuthorizationCode('cli', 'sec', 'BAD', 'https://x/cb', 'VER');
    })->throws(MercadoLivreAuthenticationException::class);
});
