<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\ProductImage\ProductImageMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

it('list faz GET produto_imagem/?produto=', function () {
    lojaIntegradaMethods(ProductImageMethods::class)->list(166737173);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/produto_imagem/?produto=166737173&limit=20&offset=0');
});

it('get faz GET produto_imagem/{id}/', function () {
    lojaIntegradaMethods(ProductImageMethods::class)->get(3);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/produto_imagem/3/');
});

it('create faz POST produto_imagem/', function () {
    $data = ['imagem_url' => 'https://x/a.png', 'produto' => '/api/v1/produto/1', 'principal' => true, 'posicao' => 0];
    lojaIntegradaMethods(ProductImageMethods::class)->create($data);
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v1/produto_imagem/', $data);
});

it('delete faz DELETE produto_imagem/{id}/', function () {
    lojaIntegradaMethods(ProductImageMethods::class)->delete(3);
    lojaIntegradaAssertSent('DELETE', 'https://api.awsli.com.br/v1/produto_imagem/3/');
});

it('getVariationImages faz GET .../grade_variacao/{id}/', function () {
    lojaIntegradaMethods(ProductImageMethods::class)->getVariationImages(3, 44);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/produto_imagem/3/grade_variacao/44/');
});

it('setVariationImages faz PUT com imagens_ids', function () {
    lojaIntegradaMethods(ProductImageMethods::class)->setVariationImages(3, 44, [216642377, 215404158]);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/produto_imagem/3/grade_variacao/44/', ['imagens_ids' => [216642377, 215404158]]);
});

it('deleteVariationImages faz DELETE .../grade_variacao/{id}/', function () {
    lojaIntegradaMethods(ProductImageMethods::class)->deleteVariationImages(3, 44);
    lojaIntegradaAssertSent('DELETE', 'https://api.awsli.com.br/v1/produto_imagem/3/grade_variacao/44/');
});
