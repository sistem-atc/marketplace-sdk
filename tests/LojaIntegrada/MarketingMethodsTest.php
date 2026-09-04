<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Marketing\MarketingMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

it('listActiveAutomations faz GET /v3/marketing/automations/active', function () {
    lojaIntegradaMethods(MarketingMethods::class)->listActiveAutomations();
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v3/marketing/automations/active');
});

it('automationRules faz GET /v3/marketing/rules/channels/{id}', function () {
    lojaIntegradaMethods(MarketingMethods::class)->automationRules(5);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v3/marketing/rules/channels/5');
});

it('listCampaigns faz GET /v3/marketing/campaign/{id} com limit/offset e filtros', function () {
    lojaIntegradaMethods(MarketingMethods::class)->listCampaigns(5, limit: 10, offset: 0, filters: ['onlyWithPhone' => 'true']);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v3/marketing/campaign/5?limit=10&offset=0&onlyWithPhone=true');
});

it('campaignDetails faz GET /v3/marketing/campaign/details/{id}', function () {
    lojaIntegradaMethods(MarketingMethods::class)->campaignDetails(9);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v3/marketing/campaign/details/9');
});

it('deleteCampaign faz DELETE /v3/marketing/campaign/{id}', function () {
    lojaIntegradaMethods(MarketingMethods::class)->deleteCampaign(9);
    lojaIntegradaAssertSent('DELETE', 'https://api.awsli.com.br/v3/marketing/campaign/9');
});

it('exportCampaigns faz GET /v3/marketing/campaign/export/{id}', function () {
    lojaIntegradaMethods(MarketingMethods::class)->exportCampaigns(5, ['clientName' => 'Ana']);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v3/marketing/campaign/export/5?clientName=Ana');
});

it('optOut faz POST /v3/marketing/automations/optout', function () {
    lojaIntegradaMethods(MarketingMethods::class)->optOut('u@e.com', 123, 456);
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v3/marketing/automations/optout', ['recipient' => 'u@e.com', 'storeId' => 123, 'automationId' => 456]);
});

it('getAutomationConfig faz GET /v3/marketing/automations/config/', function () {
    lojaIntegradaMethods(MarketingMethods::class)->getAutomationConfig();
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v3/marketing/automations/config/');
});

it('createAutomationConfig faz POST config/', function () {
    lojaIntegradaMethods(MarketingMethods::class)->createAutomationConfig(['startInterval' => '08:00:00']);
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v3/marketing/automations/config/', ['startInterval' => '08:00:00']);
});

it('updateAutomationConfig faz PUT config/', function () {
    lojaIntegradaMethods(MarketingMethods::class)->updateAutomationConfig(['endInterval' => '22:00:00']);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v3/marketing/automations/config/', ['endInterval' => '22:00:00']);
});

it('deleteAutomationConfig faz DELETE config/', function () {
    lojaIntegradaMethods(MarketingMethods::class)->deleteAutomationConfig();
    lojaIntegradaAssertSent('DELETE', 'https://api.awsli.com.br/v3/marketing/automations/config/');
});

it('createRules faz POST /v3/marketing/rules com LISTA no body', function () {
    lojaIntegradaMethods(MarketingMethods::class)->createRules([['automationId' => 5, 'triggerDelay' => 60]]);
    Http::assertSent(fn ($req) => $req->method() === 'POST'
        && $req->url() === 'https://api.awsli.com.br/v3/marketing/rules'
        && $req->data() === [['automationId' => 5, 'triggerDelay' => 60]]);
});

it('deleteRule faz DELETE /v3/marketing/rules/{id}', function () {
    lojaIntegradaMethods(MarketingMethods::class)->deleteRule(7);
    lojaIntegradaAssertSent('DELETE', 'https://api.awsli.com.br/v3/marketing/rules/7');
});

it('toggleAutomations faz POST /v3/marketing/rules/toggle', function () {
    lojaIntegradaMethods(MarketingMethods::class)->toggleAutomations(['abandoned-cart' => true]);
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v3/marketing/rules/toggle', ['abandoned-cart' => true]);
});

it('listNewsletter faz GET /v3/marketing/newsletter', function () {
    lojaIntegradaMethods(MarketingMethods::class)->listNewsletter();
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v3/marketing/newsletter');
});

it('subscribeNewsletter faz POST /v3/marketing/newsletter', function () {
    lojaIntegradaMethods(MarketingMethods::class)->subscribeNewsletter('u@e.com', 'Maria');
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v3/marketing/newsletter', ['email' => 'u@e.com', 'name' => 'Maria']);
});

it('getNewsletter faz GET /v3/marketing/newsletter/{id}', function () {
    lojaIntegradaMethods(MarketingMethods::class)->getNewsletter(3);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v3/marketing/newsletter/3');
});

it('unsubscribeNewsletter faz DELETE /v3/marketing/newsletter/{email} url-encoded', function () {
    lojaIntegradaMethods(MarketingMethods::class)->unsubscribeNewsletter('u@e.com');
    lojaIntegradaAssertSent('DELETE', 'https://api.awsli.com.br/v3/marketing/newsletter/u%40e.com');
});

it('awaitingList faz GET /v3/marketing/awaiting/list com page/pageSize', function () {
    lojaIntegradaMethods(MarketingMethods::class)->awaitingList(page: 2, pageSize: 50, filters: ['marcaId' => 7]);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v3/marketing/awaiting/list?page=2&pageSize=50&marcaId=7');
});
