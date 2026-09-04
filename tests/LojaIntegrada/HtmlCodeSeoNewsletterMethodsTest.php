<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\HtmlCode\HtmlCodeMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Newsletter\NewsletterMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Seo\SeoMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

it('htmlCode list faz GET codigo_html/', function () {
    lojaIntegradaMethods(HtmlCodeMethods::class)->list();
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/codigo_html/?limit=20&offset=0');
});

it('htmlCode get faz GET codigo_html/{id}/', function () {
    lojaIntegradaMethods(HtmlCodeMethods::class)->get(3);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/codigo_html/3/');
});

it('htmlCode create faz POST codigo_html/', function () {
    lojaIntegradaMethods(HtmlCodeMethods::class)->create(['conteudo' => '<b/>', 'tipo' => 'html', 'local_publicacao' => 'rodape']);
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v1/codigo_html/', ['conteudo' => '<b/>', 'tipo' => 'html', 'local_publicacao' => 'rodape']);
});

it('htmlCode update faz PUT codigo_html/{id}/', function () {
    lojaIntegradaMethods(HtmlCodeMethods::class)->update(3, ['descricao' => 'Alterado']);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/codigo_html/3/', ['descricao' => 'Alterado']);
});

it('seo get faz GET seo/{id}/', function () {
    lojaIntegradaMethods(SeoMethods::class)->get(99);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/seo/99/');
});

it('seo update faz PUT seo/{id}/ com title/description', function () {
    lojaIntegradaMethods(SeoMethods::class)->update(99, ['title' => 'T', 'description' => 'D']);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/seo/99/', ['title' => 'T', 'description' => 'D']);
});

it('newsletter find faz GET newsletter/?email=', function () {
    lojaIntegradaMethods(NewsletterMethods::class)->find('t@t.com');
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/newsletter/?email=t%40t.com');
});

it('newsletter subscribe faz POST newsletter/', function () {
    lojaIntegradaMethods(NewsletterMethods::class)->subscribe('t@t.com');
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v1/newsletter/', ['email' => 't@t.com']);
});
