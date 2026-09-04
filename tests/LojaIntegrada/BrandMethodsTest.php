<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Brand\BrandMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

it('list faz GET marca/ paginado', function () {
    lojaIntegradaMethods(BrandMethods::class)->list();
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/marca/?limit=20&offset=0');
});

it('get faz GET marca/{id}/', function () {
    lojaIntegradaMethods(BrandMethods::class)->get(18139613);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/marca/18139613/');
});

it('getSet usa ; e id_externo', function () {
    lojaIntegradaMethods(BrandMethods::class)->getSet(['1', '2', '3'], byExternalId: true);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/marca/set/1;2;3/?id_externo=1');
});

it('create faz POST marca/', function () {
    lojaIntegradaMethods(BrandMethods::class)->create(['nome' => 'Marca', 'apelido' => 'marca']);
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v1/marca/', ['nome' => 'Marca', 'apelido' => 'marca']);
});

it('update faz PUT marca/{id}/', function () {
    lojaIntegradaMethods(BrandMethods::class)->update(5, ['descricao' => 'Nova']);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/marca/5/', ['descricao' => 'Nova']);
});

it('delete faz DELETE marca/{id}/', function () {
    lojaIntegradaMethods(BrandMethods::class)->delete(5);
    lojaIntegradaAssertSent('DELETE', 'https://api.awsli.com.br/v1/marca/5/');
});

it('updateImageByUrl faz PATCH marca_imagem/{id}/ com imagem_url', function () {
    lojaIntegradaMethods(BrandMethods::class)->updateImageByUrl(9, 'https://x/img.png');
    lojaIntegradaAssertSent('PATCH', 'https://api.awsli.com.br/v1/marca_imagem/9/', ['imagem_url' => 'https://x/img.png']);
});

it('updateImageByFile faz PATCH multipart em marca_imagem/{id}/arquivo/', function () {
    lojaIntegradaMethods(BrandMethods::class)->updateImageByFile(9, 'BIN', 'logo.png');
    lojaIntegradaAssertSent('PATCH', 'https://api.awsli.com.br/v1/marca_imagem/9/arquivo/', ['arquivo' => 'BIN'], multipart: true);
});

it('deleteImage faz DELETE marca_imagem/{id}/', function () {
    lojaIntegradaMethods(BrandMethods::class)->deleteImage(9);
    lojaIntegradaAssertSent('DELETE', 'https://api.awsli.com.br/v1/marca_imagem/9/');
});
