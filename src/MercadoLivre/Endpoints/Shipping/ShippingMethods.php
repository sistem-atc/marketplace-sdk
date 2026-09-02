<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Shipping;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;

/**
 * Modos de envio, custos/simulacao de frete, preferencias, ME1 (frete
 * dinamico) e operacoes de estoque Fulfillment. Tudo que e "configuracao de
 * envio" e nao pertence a um shipment especifico (esses ficam em ShipmentMethods).
 */
class ShippingMethods extends BaseMethods
{
    /**
     * Metodos de envio do site (GET /sites/{site}/shipping_methods): lista de
     * {id, name, type, deliver_to, status, free_options, shipping_modes}.
     *
     * @return array<int, array<string,mixed>>
     */
    public function siteShippingMethods(string $siteId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/sites/'.rawurlencode($siteId).'/shipping_methods');
    }

    /**
     * Preferencias de envio do vendedor (GET /users/{user}/shipping_preferences):
     * modes habilitados (me1/me2/custom), logistics por modo, tags (ex.:
     * `proximity` = Envios Agora, `turbo`), picking_type, custom_calculator.
     *
     * @return array<string,mixed>
     */
    public function userShippingPreferences(int|string $userId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/shipping_preferences");
    }

    /**
     * Preferencias de envio da categoria (GET /categories/{cat}/shipping_preferences):
     * dimensoes maximas e logistics[{mode, types[]}] permitidos (self_service =
     * Flex, fulfillment, cross_docking...). `restricted` = categoria fragil.
     *
     * @return array<string,mixed>
     */
    public function categoryShippingPreferences(string $categoryId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/categories/'.rawurlencode($categoryId).'/shipping_preferences');
    }

    /**
     * Atributos obrigatorios do dominio pra calcular frete ME2
     * (GET /catalog_domains/{domain}/shipping_attributes), ex.: MLB-AUTOMOTIVE_TIRES
     * exige RIM_DIAMETER/TIRES_NUMBER. domain_id vem de /categories ou do item.
     *
     * @return array<string,mixed>
     */
    public function domainShippingAttributes(string $domainId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/catalog_domains/'.rawurlencode($domainId).'/shipping_attributes');
    }

    /**
     * Opcoes/custos de envio de um item pra um destino
     * (GET /items/{item}/shipping_options?zip_code=|city_to=). Informe `zip_code`
     * OU `city_to` (id base64 de cidade) em $query; outros filtros aceitos passam
     * direto. Em ME1 o valor e o da tabela de contingencia, nao da cotacao viva.
     *
     * @param  array<string,mixed>  $query
     * @return array<string,mixed>
     */
    public function itemShippingOptions(string $itemId, array $query): array
    {
        return $this->makeRequest(HttpMethod::GET, '/items/'.rawurlencode($itemId).'/shipping_options', $query);
    }

    /**
     * Custo aproximado que o VENDEDOR paga pelo frete gratis
     * (GET /users/{user}/shipping_options/free). Obrigatorio `item_id` OU
     * `dimensions` ("AxLxC,peso", ex.: 9x17x22,462). Envie `free_shipping`
     * (true/false) pra resposta correta; demais: item_price, listing_type_id,
     * mode, condition, logistic_type, verbose, category_id, zip_code...
     * Estimativa pra 1 unidade.
     *
     * @param  array<string,mixed>  $query
     * @return array<string,mixed>
     */
    public function freeShippingOptions(int|string $userId, array $query): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/shipping_options/free", $query);
    }

    /**
     * Valida se a API aceita o modo de envio ANTES de publicar/editar o item
     * (POST /users/{user}/shipping_modes). Body espelha o item: site_id, item_id?,
     * seller_id, title, item_price, item_currency, category_id, catalog{domain_id,
     * attributes}, dimensions, shipping{mode, ...}. Exige headers
     * `x-multichannel: true` e `X-Format-New: true`.
     *
     * @param  array<string,mixed>  $body
     * @return array<string,mixed>
     */
    public function validateShippingModes(int|string $userId, array $body): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/users/{$userId}/shipping_modes",
            body: $body,
            headers: ['x-multichannel' => 'true', 'X-Format-New' => 'true'],
        );
    }

    /**
     * Servicos logisticos contratados de um User Product
     * (GET /customers/marketplace/sites/{site}/user-products/{up}/contracts/shippability/services):
     * services[{type, direction, flavor, speed, distribution_attributes[]}].
     * legacy_attributes=true adiciona o mapeamento pra `mode`/`logistic_type` antigos.
     *
     * @return array<string,mixed>
     */
    public function shippabilityServices(string $siteId, string $userProductId, bool $legacyAttributes = false): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/customers/marketplace/sites/'.rawurlencode($siteId).'/user-products/'.rawurlencode($userProductId).'/contracts/shippability/services',
            $legacyAttributes ? ['legacy_attributes' => 'true'] : [],
        );
    }

    // ------------------------------------------------------------------
    // ME1 — frete dinamico (uso exclusivo de integradores de cotacao)
    // ------------------------------------------------------------------

    /**
     * Metricas de qualidade das cotacoes do integrador ME1
     * (GET /shipping/me1/sites/{site}/metrics?ts_from&ts_to[&seller_id]).
     * ts_* em ISO-8601 UTC (YYYY-MM-DDTHH:MM:SSZ). Token precisa ser de partner
     * de frete dinamico, senao 403.
     *
     * @return array<string,mixed>
     */
    public function me1Metrics(string $siteId, string $tsFrom, string $tsTo, int|string|null $sellerId = null): array
    {
        $query = ['ts_from' => $tsFrom, 'ts_to' => $tsTo];
        if ($sellerId !== null) $query['seller_id'] = $sellerId;

        return $this->makeRequest(HttpMethod::GET, '/shipping/me1/sites/'.rawurlencode($siteId).'/metrics', $query);
    }

    /**
     * Simula uma cotacao ME1 como o comprador veria
     * (POST /shipping/me1/v1/quotation/simulate). Body {declared_value,
     * dimensions{width,height,length,weight(g)}, destination{type: zipcode|city, value}}.
     *
     * @param  array<string,mixed>  $dimensions
     * @param  array<string,mixed>  $destination
     * @return array<string,mixed>
     */
    public function me1SimulateQuotation(float $declaredValue, array $dimensions, array $destination): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/shipping/me1/v1/quotation/simulate',
            body: ['declared_value' => $declaredValue, 'dimensions' => $dimensions, 'destination' => $destination],
        );
    }

    /**
     * Template XLSX da tabela de frete de contingencia ME1
     * (GET /shipping/me1/v1/tariff/template?site=). Resposta JSON com `content`
     * em base64 + filename/mimetype — decodifique antes de salvar.
     *
     * @return array<string,mixed>
     */
    public function me1TariffTemplate(string $site): array
    {
        return $this->makeRequest(HttpMethod::GET, '/shipping/me1/v1/tariff/template', ['site' => $site]);
    }

    /**
     * Tarifario ME1 por UUID (GET /shipping/me1/v1/tariff/{uuid}): status
     * (active/processing/error...), content base64, errors[], warnings[].
     * O UUID e o `resource_id` devolvido por me1UpdateTariff().
     *
     * @return array<string,mixed>
     */
    public function me1Tariff(string $tariffId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/shipping/me1/v1/tariff/'.rawurlencode($tariffId));
    }

    /**
     * Sobe nova tabela de frete ME1 (POST /shipping/me1/v1/tariff/update) —
     * MULTIPART: campos site, service (ex.: transportadora-17), file (xlsx) e
     * callback_url opcional. Processamento assincrono: devolve 202 com
     * resource_id; acompanhe em me1Tariff(). Vendedor precisa ter ME1 ativo.
     *
     * @param  string  $fileContents  bytes do XLSX (nao o caminho)
     * @return array<string,mixed>
     */
    public function me1UpdateTariff(string $site, string $service, string $fileContents, string $filename = 'tariff.xlsx', ?string $callbackUrl = null): array
    {
        $fields = ['site' => $site, 'service' => $service];
        if ($callbackUrl !== null) $fields['callback_url'] = $callbackUrl;

        $response = (clone $this->httpClient)
            ->attach('file', $fileContents, $filename)
            ->post('/shipping/me1/v1/tariff/update', $fields);

        if ($response->failed()) throw new MercadoLivreRequestException($response);

        return $response->json() ?? [];
    }

    // ------------------------------------------------------------------
    // Fulfillment — operacoes de estoque
    // ------------------------------------------------------------------

    /**
     * Operacoes de estoque Fulfillment (GET /stock/fulfillment/operations/search).
     * Query: seller_id (obrigatorio), inventory_id (1 ou varios separados por
     * virgula), date_from/date_to (YYYY-MM-DD; padrao ultimos 15 dias), type
     * (SALE_CONFIRMATION, INBOUND_RECEPTION...), external_references.shipment_id,
     * limit (max/padrao 1000), sort. Paginacao por SCROLL: repita a MESMA query
     * com `scroll` = paging.scroll da pagina anterior ate vir null; o scroll
     * expira em 5 min. Historico limitado aos ultimos 12 meses.
     *
     * @param  array<string,mixed>  $query
     * @return array<string,mixed>
     */
    public function fulfillmentOperationsSearch(array $query): array
    {
        return $this->makeRequest(HttpMethod::GET, '/stock/fulfillment/operations/search', $query);
    }

    /**
     * Detalhe de uma operacao de estoque Fulfillment (GET /stock/fulfillment/operations/{id}):
     * {id, seller_id, inventory_id, date_created, type, detail{available_quantity, not_available_detail[]}}.
     *
     * @return array<string,mixed>
     */
    public function fulfillmentOperation(int|string $operationId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/stock/fulfillment/operations/{$operationId}");
    }
}
